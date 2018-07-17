<html>
<body>
<form method="post" action="<?=$kernel->getRouter()->generate('app_login')?>">
    <input type="email" name="email" placeholder="email"/>
    <input type="password" name="password" placeholder="password"/>
    <input type="hidden" name="_token" value="<?=$kernel->getCsrfTokenManager()->generate('login', $request)?>"/>
    <input type="submit"/>
</form>
</body>
</html>
