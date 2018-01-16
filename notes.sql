
CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `user` varchar(25) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `title` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `body` varchar(1024) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `due` date NOT NULL,
  `created` date NOT NULL,
  `importance` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id` (`id`),
  ADD KEY `user` (`user`);

ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;
