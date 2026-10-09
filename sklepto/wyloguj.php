<?php
require_once __DIR__ . '/lib.php';
unset($_SESSION['user_id']);
flash('success', 'Wylogowano.');
redirect('index.php');
