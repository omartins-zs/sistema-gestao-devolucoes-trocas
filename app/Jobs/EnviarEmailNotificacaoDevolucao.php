<?php

namespace App\Jobs;

use App\Models\Devolucao;
use App\Models\LembreteEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarEmailNotificacaoDevolucao implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    /**
     * Número de tentativas em caso de falha
     */
    public int $tries = 3;

    /**
     * Timeout em segundos
     */
    public int $timeout = 60;

    /**
     * Backoff entre tentativas (em segundos)
     */
    public array $backoff = [30, 60, 120];

    /**
     * Create a new job instance.
     */
    public function __construct(
        private int $devolucaoId,
        private string $statusAnterior,
        private string $statusNovo
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $devolucao = Devolucao::with(['cliente', 'produto', 'produtoTroca', 'pedidoItem.pedido', 'reembolso'])->findOrFail($this->devolucaoId);

            if (!$devolucao->cliente || !$devolucao->cliente->email) {
                Log::warning('Cliente ou e-mail não encontrado para devolução', [
                    'devolucao_id' => $this->devolucaoId,
                ]);
                return;
            }

            $assunto = $this->gerarAssunto($this->statusNovo, $devolucao->tipo);
            $mensagem = $this->gerarMensagem($devolucao, $this->statusAnterior, $this->statusNovo);

            Mail::raw($mensagem, function ($message) use ($devolucao, $assunto) {
                $message->to($devolucao->cliente->email, $devolucao->cliente->nome)
                    ->subject($assunto);
            });

            LembreteEmail::create([
                'devolucao_id' => $this->devolucaoId,
                'data_envio' => now(),
                'canal' => 'email',
            ]);

            Log::info('E-mail de notificação enviado com sucesso', [
                'devolucao_id' => $this->devolucaoId,
                'cliente_email' => $devolucao->cliente->email,
                'status' => $this->statusNovo,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de notificação', [
                'devolucao_id' => $this->devolucaoId,
                'erro' => $e->getMessage(),
                'tentativa' => $this->attempts(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de envio de e-mail falhou após todas as tentativas', [
            'devolucao_id' => $this->devolucaoId,
            'erro' => $exception->getMessage(),
            'tentativas' => $this->tries,
        ]);
    }

    /**
     * Gera o assunto do e-mail baseado no status
     */
    private function gerarAssunto(string $status, string $tipo = 'devolucao'): string
    {
        $tipoTexto = $tipo === 'troca' ? 'troca' : 'devolução';
        
        return match ($status) {
            'aprovada' => "Sua {$tipoTexto} foi aprovada",
            'recusada' => "Sua {$tipoTexto} foi recusada",
            'concluida' => "Sua {$tipoTexto} foi concluída",
            default => "Atualização na sua solicitação de {$tipoTexto}",
        };
    }

    /**
     * Gera a mensagem do e-mail
     */
    private function gerarMensagem(Devolucao $devolucao, string $statusAnterior, string $statusNovo): string
    {
        $clienteNome = $devolucao->cliente->nome;
        $produtoNome = $devolucao->produto->nome;
        $quantidade = $devolucao->quantidade;
        $motivo = $devolucao->motivo;
        $tipo = $devolucao->tipo === 'troca' ? 'troca' : 'devolução';

        $mensagem = "Olá {$clienteNome},\n\n";
        $mensagem .= "Informamos que sua solicitação de {$tipo} foi atualizada.\n\n";
        $mensagem .= "Detalhes da {$tipo}:\n";
        $mensagem .= "- Produto: {$produtoNome}\n";
        $mensagem .= "- Quantidade: {$quantidade}\n";
        $mensagem .= "- Motivo: {$motivo}\n";
        
        if ($devolucao->tipo === 'troca' && $devolucao->motivo_troca) {
            $mensagem .= "- Motivo da troca: {$devolucao->motivo_troca}\n";
        }
        
        if ($devolucao->tipo === 'troca' && $devolucao->produtoTroca) {
            $mensagem .= "- Produto de troca: {$devolucao->produtoTroca->nome}\n";
        }
        
        $mensagem .= "- Status anterior: " . ucfirst($statusAnterior) . "\n";
        $mensagem .= "- Novo status: " . ucfirst($statusNovo) . "\n\n";

        // Código de rastreamento
        if ($devolucao->codigo_rastreamento) {
            $mensagem .= "📦 CÓDIGO DE RASTREAMENTO: {$devolucao->codigo_rastreamento}\n";
            $mensagem .= "Você pode rastrear o envio do produto usando este código.\n\n";
        }

        if ($devolucao->observacoes) {
            $mensagem .= "Observações: {$devolucao->observacoes}\n\n";
        }

        $mensagemTexto = match ($statusNovo) {
            'aprovada' => $devolucao->tipo === 'troca' 
                ? "✅ Sua troca foi APROVADA!\n\nPor favor, envie o produto para o endereço que será informado em breve. O produto de troca será enviado após o recebimento e análise do produto devolvido.\n\n"
                : "✅ Sua devolução foi APROVADA!\n\nPor favor, envie o produto para o endereço que será informado em breve. Após o recebimento, o reembolso será processado.\n\n",
            'recusada' => $devolucao->tipo === 'troca'
                ? "❌ Infelizmente, sua troca foi RECUSADA.\n\nMotivo: {$devolucao->observacoes}\n\nEntre em contato conosco para mais informações ou para contestar esta decisão.\n\n"
                : "❌ Infelizmente, sua devolução foi RECUSADA.\n\nMotivo: {$devolucao->observacoes}\n\nEntre em contato conosco para mais informações ou para contestar esta decisão.\n\n",
            'concluida' => $devolucao->tipo === 'troca'
                ? "✅ Sua troca foi CONCLUÍDA!\n\nO produto foi recebido e está em análise. O produto de troca será enviado em breve e você receberá o código de rastreamento por e-mail.\n\n"
                : "✅ Sua devolução foi CONCLUÍDA!\n\nO produto foi recebido e está em análise. O reembolso será processado e o valor retornará em CRÉDITOS na plataforma em até 3 dias úteis.\n\n",
            default => "Acompanhe o status da sua {$tipo} em nosso sistema.\n\n",
        };

        $mensagem .= $mensagemTexto;

        // Informações sobre reembolso se aplicável
        if ($devolucao->tipo === 'devolucao' && $devolucao->reembolso) {
            $reembolso = $devolucao->reembolso;
            $mensagem .= "💰 INFORMAÇÕES SOBRE REEMBOLSO:\n";
            $mensagem .= "- Valor: R$ " . number_format($reembolso->valor, 2, ',', '.') . "\n";
            
            if ($reembolso->autorizado) {
                $mensagem .= "- Status: AUTORIZADO ✅\n";
                $mensagem .= "- O reembolso foi autorizado e o dinheiro retornará para o método de pagamento da compra original em até 3 dias úteis.\n\n";
            } else {
                $mensagem .= "- Status: AGUARDANDO AUTORIZAÇÃO ⏳\n";
                $mensagem .= "- O reembolso está aguardando autorização. Você será notificado assim que for aprovado.\n\n";
            }
        }

        $mensagem .= "\nAtenciosamente,\nEquipe de Atendimento";

        return $mensagem;
    }
}
