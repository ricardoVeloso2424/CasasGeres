<?php

namespace App\Http\Controllers;

use App\Mail\NewContactMessageReceived;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Throwable;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('contact.index', [
            'seo' => [
                'title' => 'Contactos para reserva direta no Geres',
                'description' => 'Entre em contacto por telefone, WhatsApp, email ou formulario para pedir disponibilidade e reservar diretamente no Geres.',
                'canonical' => route('contact.index'),
                'image' => config('site.default_og_image'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            if ($request->filled('website')) {
                $validator->errors()->add('contact', 'Nao foi possivel processar o formulario. Tente novamente.');
            }

            if (! $request->filled('email') && ! $request->filled('phone')) {
                $validator->errors()->add('contact', 'Indique pelo menos email ou telefone para podermos responder.');
            }
        });

        $data = $validator->validate();

        $contactMessage = ContactMessage::query()->create($data);

        $this->notifyOwner($contactMessage);

        return back()->with('status', 'Mensagem recebida. Entraremos em contacto assim que possivel.');
    }

    private function notifyOwner(ContactMessage $contactMessage): void
    {
        $ownerEmail = trim((string) config('site.email'));

        if ($ownerEmail === '') {
            Log::warning('SITE_EMAIL is not configured; contact message email was not sent.', [
                'contact_message_id' => $contactMessage->id,
            ]);

            return;
        }

        try {
            Mail::to($ownerEmail)->send(new NewContactMessageReceived($contactMessage));
        } catch (Throwable $exception) {
            Log::error('Contact message email could not be sent.', [
                'contact_message_id' => $contactMessage->id,
                'exception' => $exception::class,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
