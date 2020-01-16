<?php
require('core.php');
session_unset('onlol_adminpanel_logged');
session_unset('onlol_adminpanel_logged_user');
admin::redirect('login.php');