<?php
/**
 * @var \App\Kernel $kernel
 */
?>
<html>
<body>
<ul>
    <li><a href="<?=$kernel->getRouter()->generate('app_home')?>">Home</a></li>
    <li><a href="<?=$kernel->getRouter()->generate('app_profile')?>">Profile</a></li>
    <li><a href="<?=$kernel->getRouter()->generate('app_login')?>">Login</a></li>
    <li><a href="<?=$kernel->getRouter()->generate('app_logout')?>">Logout</a></li>
</ul>
</body>
</html>
