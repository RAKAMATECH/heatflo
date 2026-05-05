<p><strong>New Lead</strong></p>

<p><strong>Name:</strong> {{ $lead->name }}</p>
<p><strong>Email:</strong> {{ $lead->email ?? '-' }}</p>
<p><strong>Phone:</strong> {{ $lead->phone ?? '-' }}</p>
<p><strong>Service:</strong> {{ $lead->service ?? '-' }}</p>
<p><strong>Location:</strong> {{ $lead->location ?? '-' }}</p>
<p><strong>Subject:</strong> {{ $lead->subject ?? '-' }}</p>
<p><strong>Source:</strong> {{ $lead->source ?? '-' }}</p>

<p><strong>Message:</strong></p>
<p>{!! nl2br(e($lead->message)) !!}</p>

<p><small>Lead ID: {{ $lead->id }}</small></p>
