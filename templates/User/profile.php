<?php
/**
 * @var \App\Kernel $kernel
 * @var \Symfony\Component\HttpFoundation\Request $request
 * @var \App\Entity\User $user
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
<div>
    <p>Your balance is <?=number_format($user->getBalance(), 2)?></p>
    <form action="<?=$kernel->getRouter()->generate('app_payout')?>" method="post">
        <p>Payout to /dev/null:</p>
        <input type="text" name="sum" placeholder="sum"/>
        <input type="hidden" name="_token" value="<?=$kernel->getCsrfTokenManager()->generate('payout', $request)?>"/>
        <input type="submit"/>
    </form>
</div>
</body>
</html>
