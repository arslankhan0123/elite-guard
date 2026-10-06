<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
  body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
  .container { max-width: 600px; margin: 30px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,.1); }
  .header { background: {{ $type === 'deactivated' ? '#dc3545' : '#ffc107' }}; padding: 28px 32px; text-align: center; }
  .header h1 { margin: 0; color: {{ $type === 'deactivated' ? '#fff' : '#333' }}; font-size: 20px; }
  .logo { font-size: 24px; font-weight: 900; color: {{ $type === 'deactivated' ? '#fff' : '#333' }}; letter-spacing: -0.5px; margin-bottom: 6px; }
  .body { padding: 32px; color: #444; line-height: 1.7; }
  .body p { margin: 0 0 14px; }
  .footer { background: #f8f8f8; padding: 18px 32px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
  .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700;
           background: {{ $type === 'deactivated' ? '#dc3545' : '#ffc107' }};
           color: {{ $type === 'deactivated' ? '#fff' : '#333' }}; margin-bottom: 16px; }
  .cta { display: inline-block; margin-top: 20px; padding: 12px 28px; background: #ffc107; color: #333;
         border-radius: 6px; text-decoration: none; font-weight: 700; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <div class="logo">⚡ Elite Guard</div>
    <h1>{{ $subject }}</h1>
  </div>
  <div class="body">
    <div class="badge">{{ $type === 'deactivated' ? 'Account Deactivated' : 'Action Required' }}</div>
    <p>Hello, <strong>{{ $tenant->name }}</strong>,</p>
    @foreach($bodyLines as $line)
      <p>{!! $line !!}</p>
    @endforeach
    <a href="mailto:support@eliteguardinc.com" class="cta">Contact Support</a>
  </div>
  <div class="footer">
    &copy; {{ date('Y') }} Elite Guard Inc. &bull; This is an automated message, please do not reply directly.
  </div>
</div>
</body>
</html>
