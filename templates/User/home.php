<?php
/**
 * @var \App\Kernel $kernel
 */
?>
<html>
<body>
<ul>
    <li><a href="<?=$kernel->getRouter()->generate(\App\Routes::HOME)?>">Home</a></li>
    <li><a href="<?=$kernel->getRouter()->generate(\App\Routes::PROFILE)?>">Profile</a></li>
    <li><a href="<?=$kernel->getRouter()->generate(\App\Routes::LOGIN)?>">Login</a></li>
    <li><a href="<?=$kernel->getRouter()->generate(\App\Routes::LOGOUT, ['_token' => $kernel->getCsrfTokenManager()->generate('logout', $request)])?>">Logout</a></li>
</ul>
</body>
</html>
