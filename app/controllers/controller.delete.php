<?php

include_once('../models/model.users.class.php');

$users = new users('', '', '');

$users->excluirusers($_GET['id']);

header("Location: ../views/crud/view.read.php");
