<?php

namespace App\Jobs;

use App\Models\Reembolso;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarEmailNotificacaoReembolso implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;
    public array $backoff = [30, 60, 120];

    public function __construct(
        private int $reembolsoId,
        private bool $autorizado
    ) {
    }

    public function handle(): void
    {
        try {
            $reembolso = Reembolso::with(['cliente', 'devolucao.produto'])->findOrFail($this->reembolsoId);

            if (!$reembolso->cliente || !$reembolso->cliente->email) {
                Log::warning('Cliente ou e-mail não encontrado para reembolso', [
                    'reembolso_id' => $this->reembolsoId,
                ]);
                return;
            }

            $assunto = $this->autorizado 
                ? "Seu reembolso foi autorizado" 
                : "Seu reembolso foi negado";

            $mensagem = $this->gerarMensagem($reembolso);

            Mail::raw($mensagem, function ($message) use ($reembolso, $assunto) {
                $message->to($reembolso->cliente->email, $reembolso->cliente->nome)
                    ->subject($assunto);
            });

            Log::info('E-mail de notificação de reembolso enviado com sucesso', [
                'reembolso_id' => $this->reembolsoId,
                'cliente_email' => $reembolso->cliente->email,
                'autorizado' => $this->autorizado,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar e-mail de notificação de reembolso', [
                'reembolso_id' => $this->reembolsoId,
                'erro' => $e->getMessage(),
                'tentativa' => $this->attempts(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Job de envio de e-mail de reembolso falhou após todas as tentativas', [
            'reembolso_id' => $this->reembolsoId,
            'erro' => $exception->getMessage(),
            'tentativas' => $this->tries,
        ]);
    }

    private function gerarMensagem(Reembolso $reembolso): string
    {
        $clienteNome = $reembolso->cliente->nome;
        $valor = number_format($reembolso->valor, 2, ',', '.');
        $produtoNome = $reembolso->devolucao->produto->nome;

        $mensagem = "Olá {$clienteNome},\n\n";

        if ($this->autorizado) {
            $mensagem .= "✅ Seu reembolso foi AUTORIZADO!\n\n";
            $mensagem .= "Detalhes do reembolso:\n";
            $mensagem .= "- Produto: {$produtoNome}\n";
            $mensagem .= "- Valor: R$ {$valor}\n\n";
            $mensagem .= "💰 O dinheiro retornará para o método de pagamento da compra original em até 3 dias úteis.\n\n";
            $mensagem .= "Você receberá uma notificação quando o reembolso for processado.\n\n";
        } else {
            $mensagem .= "❌ Seu reembolso foi NEGADO.\n\n";
            $mensagem .= "Detalhes:\n";
            $mensagem .= "- Produto: {$produtoNome}\n";
            $mensagem .= "- Valor solicitado: R$ {$valor}\n\n";
            
            if ($reembolso->observacoes) {
                $mensagem .= "Motivo: {$reembolso->observacoes}\n\n";
            }
            
            $mensagem .= "Entre em contato conosco para mais informações ou para contestar esta decisão.\n\n";
        }

        $mensagem .= "Atenciosamente,\nEquipe de Atendimento";

        return $mensagem;
    }
}
