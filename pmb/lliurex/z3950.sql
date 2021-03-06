-- Modificamos duración de los carnets a 10 anyos

UPDATE empr_categ SET duree_adhesion = '3650';


-- En nuevas instalaciones

-- Introducimos nuevos servidores z3950

DROP TABLE IF EXISTS `z_bib_bak`;
CREATE TABLE IF NOT EXISTS `z_bib_bak` (
  `bib_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `bib_nom` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `search_type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `port` varchar(6) COLLATE utf8_unicode_ci DEFAULT NULL,
  `base` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `format` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `auth_user` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `auth_pass` varchar(250) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `sutrs_lang` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `fichier_func` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`bib_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1;

INSERT INTO `z_bib_bak` VALUES
(null,'Biblioteca Nacional','CATALOG','sigb.bne.es','2200','Unicorn','USMARC','','','',''),
(null,'Biblioteca Valenciana','CATALOG','bvnpz3950.gva.es','2102','ABNET_DB','ISO 8859-1','','','',''),
(null,'Congreso de los Diputados','CATALOG','biblioteca.congreso.es','2100','ABSYSBCD','USMARC','','','',''),
(null,'REBECA','CATALOG','catalogos.mecd.es','220','ABNET_REBECA','ISO 8859-1','','','',''),
(null,'REBIUN','CATALOG','rebiun.crue.org','210','ABSYSREBIUN','USMARC','','','',''),
(null,'Red de Bibliotecas del CSIC','CATALOG','eu00.alma.exlibrisgroup.com','210','34CSIC_INST','MARC21','','','',''),
(null,'Catálogo Colectivo Bibliotecas Públicas','CATALOG','catalogos.mecd.es','212','ABNET_DB','ISO 8859-1','','','','');

INSERT INTO `z_bib` (`bib_nom`, `search_type`, `url`, `port`, `base`, `format`, `auth_user`, `auth_pass`, `sutrs_lang`, `fichier_func`)
SELECT DISTINCT A.bib_nom, A.search_type, A.url, A.port, A.base, A.format, A.auth_user, A.auth_pass, A.sutrs_lang, A.fichier_func
FROM `z_bib_bak` A
LEFT JOIN `z_bib` B
ON A.bib_nom = B.bib_nom
WHERE B.bib_nom IS NULL;


DROP TABLE IF EXISTS `z_bib_bak`;
