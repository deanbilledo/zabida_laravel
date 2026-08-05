<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Access restricted | ZABIDA</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500&family=Work+Sans:wght@400;500&display=swap" rel="stylesheet">
<style>
  body { margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center; background:#EEF2EE; color:#17303D; font-family:'Work Sans',sans-serif; text-align:center; padding:24px; }
  .card { max-width:440px; }
  h1 { font-family:'Fraunces',serif; font-size:2rem; margin:0 0 12px; }
  p { line-height:1.6; color:rgba(23,48,61,0.7); margin:0 0 24px; }
  a { display:inline-block; background:#17303D; color:#EEF2EE; padding:12px 28px; text-decoration:none; font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em; }
  a:hover { background:#B14A2E; }
</style>
</head>
<body>
  <div class="card">
    <h1>This area is for ZABIDA admins</h1>
    <p>You don't have access to this page. If you're an admin, please sign in — otherwise this section isn't meant for you.</p>
    <a href="{{ url('/admin/login') }}">Go to admin sign-in</a>
  </div>
</body>
</html>
