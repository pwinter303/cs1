

CREATE TABLE `admissions_info`   (
`UNITID`   INTEGER(6)   NULL COMMENT 'Unique identification number of the institution',
`ADMCON1`   INTEGER(2)   NULL COMMENT 'Secondary school GPA',
`ADMCON2`   INTEGER(2)   NULL COMMENT 'Secondary school rank',
`ADMCON3`   INTEGER(2)   NULL COMMENT 'Secondary school record',
`ADMCON4`   INTEGER(2)   NULL COMMENT 'Completion of college-preparatory program',
`ADMCON5`   INTEGER(2)   NULL COMMENT 'Recommendations',
`ADMCON6`   INTEGER(2)   NULL COMMENT 'Formal demonstration of competencies',
`ADMCON7`   INTEGER(2)   NULL COMMENT 'Admission test scores',
`ADMCON8`   INTEGER(2)   NULL COMMENT 'TOEFL (Test of English as a Foreign Language',
`ADMCON9`   INTEGER(2)   NULL COMMENT 'Other Test (Wonderlic, WISC-III, etc.)',
`APPLCN`   INTEGER(6)   NULL COMMENT 'Applicants total',
`APPLCNM`   INTEGER(6)   NULL COMMENT 'Applicants men',
`APPLCNW`   INTEGER(6)   NULL COMMENT 'Applicants women',
`ADMSSN`   INTEGER(6)   NULL COMMENT 'Admissions total',
`ADMSSNM`   INTEGER(6)   NULL COMMENT 'Admissions men',
`ADMSSNW`   INTEGER(6)   NULL COMMENT 'Admissions women',
`ENRLT`   INTEGER(6)   NULL COMMENT 'Enrolled total',
`ENRLM`   INTEGER(6)   NULL COMMENT 'Enrolled  men',
`ENRLW`   INTEGER(6)   NULL COMMENT 'Enrolled  women',
`ENRLFT`   INTEGER(6)   NULL COMMENT 'Enrolled full time total',
`ENRLFTM`   INTEGER(6)   NULL COMMENT 'Enrolled full time men',
`ENRLFTW`   INTEGER(6)   NULL COMMENT 'Enrolled full time women',
`ENRLPT`   INTEGER(6)   NULL COMMENT 'Enrolled part time total',
`ENRLPTM`   INTEGER(6)   NULL COMMENT 'Enrolled part time men',
`ENRLPTW`   INTEGER(6)   NULL COMMENT 'Enrolled part time women',
`SATNUM`   INTEGER(6)   NULL COMMENT 'Number of first-time degree/certificate-seeking students submitting SAT scores',
`SATPCT`   INTEGER(3)   NULL COMMENT 'Percent of first-time degree/certificate-seeking students submitting SAT scores',
`ACTNUM`   INTEGER(6)   NULL COMMENT 'Number of first-time degree/certificate-seeking students submitting ACT scores',
`ACTPCT`   INTEGER(3)   NULL COMMENT 'Percent of first-time degree/certificate-seeking students submitting ACT scores',
`SATVR25`   INTEGER(3)   NULL COMMENT 'SAT Critical Reading 25th percentile score',
`SATVR75`   INTEGER(3)   NULL COMMENT 'SAT Critical Reading 75th percentile score',
`SATMT25`   INTEGER(3)   NULL COMMENT 'SAT Math 25th percentile score',
`SATMT75`   INTEGER(3)   NULL COMMENT 'SAT Math 75th percentile score',
`SATWR25`   INTEGER(3)   NULL COMMENT 'SAT Writing 25th percentile score',
`SATWR75`   INTEGER(3)   NULL COMMENT 'SAT Writing 75th percentile score',
`ACTCM25`   INTEGER(3)   NULL COMMENT 'ACT Composite 25th percentile score',
`ACTCM75`   INTEGER(3)   NULL COMMENT 'ACT Composite 75th percentile score',
`ACTEN25`   INTEGER(3)   NULL COMMENT 'ACT English 25th percentile score',
`ACTEN75`   INTEGER(3)   NULL COMMENT 'ACT English 75th percentile score',
`ACTMT25`   INTEGER(3)   NULL COMMENT 'ACT Math 25th percentile score',
`ACTMT75`   INTEGER(3)   NULL COMMENT 'ACT Math 75th percentile score',
`ACTWR25`   INTEGER(3)   NULL COMMENT 'ACT Writing 25th percentile score',
`ACTWR75`   INTEGER(3)   NULL COMMENT 'ACT Writing 75th percentile score',
PRIMARY KEY ( UNITID )   );


CREATE TABLE IF NOT EXISTS `college_search`.`trips` (
  `trip_id` INT(11) NOT NULL AUTO_INCREMENT,
  `trip_name` VARCHAR(145) NULL,
  `customer_id` INT(11) NOT NULL,
  INDEX `fk_trips_customer1_idx` (`customer_id` ASC),
  PRIMARY KEY (`trip_id`),
  CONSTRAINT `fk_trips_customer1`
    FOREIGN KEY (`customer_id`)
    REFERENCES `college_search`.`customer` (`customer_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB

CREATE TABLE IF NOT EXISTS `college_search`.`trip_points` (
  `trip_point_id` INT(11) NOT NULL AUTO_INCREMENT,
  `UNITID` INT(6) NULL,
  `point_type_cd` VARCHAR(10) NULL,
  `address` VARCHAR(145) NULL,
  `addr_unitid_cd` VARCHAR(5) NULL,
  `trip_id` INT(11) NOT NULL,
  PRIMARY KEY (`trip_point_id`, `trip_id`),
  INDEX `fk_trip_points_trips1_idx` (`trip_id` ASC),
  CONSTRAINT `fk_trip_points_trips1`
    FOREIGN KEY (`trip_id`)
    REFERENCES `college_search`.`trips` (`trip_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB