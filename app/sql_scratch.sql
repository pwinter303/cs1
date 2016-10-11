=================   LOADS   =======================

truncate admissions_info;

load data local infile 'c:/tmp/adm2014_data_stata.csv' into table admissions_info fields terminated by ','
  enclosed by '"' lines terminated by '\n' IGNORE 1 LINES (UNITID,	ADMCON1,	ADMCON2,	ADMCON3,	ADMCON4,	ADMCON5,	ADMCON6,	ADMCON7,	ADMCON8,	ADMCON9,	@ignore,	APPLCN,	@ignore,	APPLCNM,	@ignore,	APPLCNW,	@ignore,	ADMSSN,	@ignore,	ADMSSNM,	@ignore,	ADMSSNW,	@ignore,	ENRLT,	@ignore,	ENRLM,	@ignore,	ENRLW,	@ignore,	ENRLFT,	@ignore,	ENRLFTM,	@ignore,	ENRLFTW,	@ignore,	ENRLPT,	@ignore,	ENRLPTM,	@ignore,	ENRLPTW,	@ignore,	SATNUM,	@ignore,	SATPCT,	@ignore,	ACTNUM,	@ignore,	ACTPCT,	@ignore,	SATVR25,	@ignore,	SATVR75,	@ignore,	SATMT25,	@ignore,	SATMT75,	@ignore,	SATWR25,	@ignore,	SATWR75,	@ignore,	ACTCM25,	@ignore,	ACTCM75,	@ignore,	ACTEN25,	@ignore,	ACTEN75,	@ignore,	ACTMT25,	@ignore,	ACTMT75,	@ignore,	ACTWR25,	@ignore,	ACTWR75)


load data local infile 'c:/tmp/hd2014.csv' into table institutions fields terminated by ','
  enclosed by '"' lines terminated by '\n' IGNORE 1 LINES;


load data local infile 'c:/tmp/sportXREF.csv' into table sportsname_unitid_xrefcredential_status fields terminated by ','
  enclosed by '"' lines terminated by '\n';

==== ALTER =======================================
alter table sports change UNITID unitid INT(6) null;

==== SQL =======================================

SELECT inst.INSTNM, INSTSIZE FROM institutions inst where INST.INSTSIZE > 2 AND INST.ICLEVEL = 1 and INST.UNITID NOT IN (SELECT UNITID FROM college_search.admissions_info); 

SELECT inst.INSTNM, adm.* FROM college_search.admissions_info adm, institutions inst where adm.UNITID = inst.UNITID and INST.ICLEVEL in (1) and instnm like '%Princet%'


select  unitid, instnm, STABBR, CITY,ZIP,WEBADDR from institutions;
select * from institutions where instnm like '%Dartm%';
select * from institutions where WEBADDR like '%summ%';
select * from sports, sports_decodes where sports.sport_cd = sports_decodes.sport_cd and college_nm like '%Summit%';
select LOCALE, count(*) from institutions group by locale;
select * from criteria;
select * from inst_size;
select * from sports where sport_cd = 'WCR';
select distinct college_nm from sports where unitid is null;
UPDATE sports t1
        INNER JOIN sportsname_unitid_xref t2
             ON t1.college_nm = t2.sportsname
SET t1.unitid = t2.unitid
WHERE t1.unitid is null;

SET @lat=40.2701500, @long=-74.7781500;
select instnm,
   (((acos(sin((@lat*pi()/180)) * sin((`latitude`*pi()/180))+cos((@lat*pi()/180))
    * cos((`latitude`*pi()/180)) * cos(((@long- `longitude`)*pi()/180))))*180/pi())*60*1.1515)
    AS distance
    from institutions having distance<150;

select instnm as name, locale_decode as locale, city, stabbr as state_cd, instsize_decode as school_size ,(((acos(sin((42.0400000*pi()/180)) * sin((`latitude`*pi()/180))+cos((42.0400000*pi()/180)) * cos((`latitude`*pi()/180)) * cos(((-70.6700000- `longitude`)*pi()/180))))*180/pi())*60*1.1515) AS distance from institutions, decode_instsize, decode_locale where institutions.instsize = decode_instsize.instsize and institutions.locale = decode_locale.locale having distance < 400 and 30 < distance ;

select * from institutions where iclevel = 1;
select * from zip_codes where postal_code = 22202;

select * from institutions where LONGITUDE is null;