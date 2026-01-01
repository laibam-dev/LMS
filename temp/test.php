<?php
echo password_hash("admin123", PASSWORD_DEFAULT);
UPDATE users
SET password = '$2y$10$zfh3ZL11MKKin0oqTjgZMOnRdSc2DUbSrwmqqZN1uEcjOxh6qKAsO'
WHERE email = 'admin@gmail.com';