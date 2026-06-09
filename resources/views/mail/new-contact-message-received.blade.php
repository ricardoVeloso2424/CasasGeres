<h1>Nova mensagem de contacto</h1>

<p>Foi recebida uma nova mensagem pelo formulario publico.</p>

<dl>
    <dt>Nome</dt>
    <dd>{{ $contactMessage->name }}</dd>

    <dt>Email</dt>
    <dd>{{ $contactMessage->email ?: '-' }}</dd>

    <dt>Telefone</dt>
    <dd>{{ $contactMessage->phone ?: '-' }}</dd>

    <dt>Assunto</dt>
    <dd>{{ $contactMessage->subject ?: '-' }}</dd>

    <dt>Mensagem</dt>
    <dd>{{ $contactMessage->message }}</dd>
</dl>

<p>
    <a href="{{ route('admin.contact-messages.show', $contactMessage) }}">Abrir mensagem no admin</a>
</p>
