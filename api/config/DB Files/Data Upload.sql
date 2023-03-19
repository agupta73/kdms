-- /////////////////////////////////////////////
-- // To populate Meta data for Duty Location 
-- ////////////////////////////////////////////
INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('TLDR',
'Management - Team Leader',
'General Management serves from all the locations',
3);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('GT1',
'Gate No. 1',
'Traffice Management at Gate number 1',
4);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('GT15',
'Gate No. 1.5',
'Traffic Management at Gate number 1.5',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('GT2',
'Gate No. 2',
'Traffic Management at Gate number 2',
4);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('BRDG',
'Bridge',
'Traffic Management at Bridge',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('GMK',
'Gow Mukh',
'Traffic Management at Gow Mukh',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('MVD',
'Mandir Vaishnavi Devi',
'Devotee Seva at Vaishnavi Devi Temple',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('SM',
'Shiv Mandir',
'Devotee Seva at Shiva Temple',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('LNM',
'Lakshmi Narayan Mandir',
'Devotee Seva at Lakshmi Narayan Temple',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('HM',
'Hanuman Mandir',
'Devotee Seva at Hanuman Temple',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('MJM',
'Maharaaj Ji Mandir',
'Devotee Seva from inside Maharaaj Ji Temple',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('MJMT',
'Maharaaj Ji Mandir Traffic',
'Traffic Management from Maharaaj Ji Templa',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('GT3',
'Gate No. 3',
'Traffice Management at Gate number 3',
3);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('GKBG',
'Goving Kuti to Bhandar Ghar',
'Traffice Management from Govind Kuti to Bhandar Ghar',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('BGDM',
'Bhandar Ghar to Dharamshala Mor',
'Traffice Management from Bhandar Ghar to Dharamshala Mor',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('DCD',
'Dharamshala Corridor',
'Traffice Management at Dharamshala Corridor',
3);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('BBR',
'Back Bridge',
'Traffice Management at Back Bridge',
2);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('BGSS',
'Back Bridge to Shoe Stand',
'Traffice Management from Back Bridge to Shoe Stand',
8);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('RFT1',
'Refreshment Team 1',
'Refreshment Seva team',
8);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('GCT',
'Garbage Collection Team',
'Garbage Colection to keep the temple clean',
12);

INSERT INTO `duty_location_master`
(`Duty_Location_Key`,
`Duty_Location_Name`,
`Duty_Location_Description`,
`Officers_Required`)
VALUES
('RSV',
'Reserve',
'Reserve for releaving offices on duty for short periods',
4);

-- /////////////////////////////////////////////
-- // To populate Records from 2022JB
-- ////////////////////////////////////////////
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'TLDR-2022JB-01',
'P16815003',
'',
'',
'TLDR',
'2022JB',
'Unassigned'
);

INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'TLDR-2022JB-02',
'P16838101',
'',
'',
'TLDR',
'2022JB',
'Unassigned'
);

INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'TLDR-2022JB-03',
'P16266337',
'',
'',
'TLDR',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT1-2022JB-01',
'P16205750',
'',
'',
'GT1',
'2022JB',
'Unassigned'
);

INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT1-2022JB-02',
'P16150320',
'',
'',
'GT1',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT1-2022JB-03',
'P16976960',
'',
'',
'GT1',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT1-2022JB-04',
'P18950431',
'',
'',
'GT1',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT15-2022JB-01',
'P16281588',
'',
'',
'GT15',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT15-2022JB-02',
'P16539920',
'',
'',
'GT15',
'2022JB',
'Unassigned'
);

INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BRDG-2022JB-01',
'P16335040',
'',
'',
'BRDG',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BRDG-2022JB-02',
'P220612550',
'',
'',
'BRDG',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GMK-2022JB-01',
'P190612920',
'',
'',
'GMK',
'2022JB',
'Unassigned'
);

INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GMK-2022JB-02',
'P220610955',
'',
'',
'GMK',
'2022JB',
'Unassigned'
);  
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT2-2022JB-01',
'P190612314',
'',
'',
'GT2',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT2-2022JB-02',
'P16527958',
'',
'',
'GT2',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT2-2022JB-03',
'P16833938',
'',
'',
'GT2',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT2-2022JB-04',
'P16447656',
'',
'',
'GT2',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'MVD-2022JB-01',
'P17131580',
'',
'',
'MVD',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'SM-2022JB-01',
'P220613986',
'',
'',
'SM',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'LNM-2022JB-01',
'P16244590',
'',
'',
'LNM',
'2022JB',
'Unassigned'
);

INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'HM-2022JB-01',
'P16709081',
'',
'',
'HM',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'MJMT-2022JB-01',
'P16508106',
'',
'',
'MJMT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT3-2022JB-01',
'P16624329',
'',
'',
'GT3',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT3-2022JB-02',
'P16467451',
'',
'',
'GT3',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GT3-2022JB-03',
'P16145873',
'',
'',
'GT3',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GKBG-2022JB-01',
'P22061360',
'',
'',
'GKBG',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BGDM-2022JB-01',
'P16359273',
'',
'',
'BGDM',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BGDM-2022JB-02',
'P16359273',
'',
'',
'BGDM',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'DCD-2022JB-01',
'P16364582',
'',
'',
'DCD',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'DCD-2022JB-02',
'P220613269',
'',
'',
'DCD',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'DCD-2022JB-03',
'P220612969',
'',
'',
'DCD',
'2022JB',
'Unassigned'
);
 
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BBR-2022JB-01',
'P16715655',
'',
'',
'BBR',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BBR-2022JB-02',
'P190614132',
'',
'',
'BBR',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BGSS-2022JB-01',
'P18874734',
'',
'',
'BGSS',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BGSS-2022JB-02',
'P220612660',
'',
'',
'BGSS',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BGSS-2022JB-03',
'P16952564',
'',
'',
'BGSS',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BGSS-2022JB-04',
'P220613391',
'',
'',
'BGSS',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BGSS-2022JB-05',
'P220613160',
'',
'',
'BGSS',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BGSS-2022JB-06',
'P18306845',
'',
'',
'BGSS',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BGSS-2022JB-07',
'P190613401',
'',
'',
'BGSS',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'BGSS-2022JB-08',
'P220612782',
'',
'',
'BGSS',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'RFT1-2022JB-01',
'P18165479',
'',
'',
'RFT1',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'RFT1-2022JB-02',
'P16287577',
'',
'',
'RFT1',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'RFT1-2022JB-03',
'P16541686',
'',
'',
'RFT1',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'RFT1-2022JB-04',
'P16125746',
'',
'',
'RFT1',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'RFT1-2022JB-05',
'P2206147',
'',
'',
'RFT1',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'RFT1-2022JB-06',
'P16351559',
'',
'',
'RFT1',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'RFT1-2022JB-07',
'P16771187',
'',
'',
'RFT1',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GCT-2022JB-01',
'P18950403',
'',
'',
'GCT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GCT-2022JB-02',
'P18950021',
'',
'',
'GCT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GCT-2022JB-03',
'P16432938',
'',
'',
'GCT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GCT-2022JB-04',
'P220613910',
'',
'',
'GCT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GCT-2022JB-05',
'P22061467',
'',
'',
'GCT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GCT-2022JB-06',
'P220613110',
'',
'',
'GCT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GCT-2022JB-07',
'P220612333',
'',
'',
'GCT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GCT-2022JB-08',
'P220613693',
'',
'',
'GCT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GCT-2022JB-09',
'P220613631',
'',
'',
'GCT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GCT-2022JB-10',
'P16642813',
'',
'',
'GCT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'GCT-2022JB-11',
'P220612421',
'',
'',
'GCT',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'RSV-2022JB-01',
'P18951389',
'',
'',
'RSV',
'2022JB',
'Unassigned'
);
INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'RSV-2022JB-02',
'P220614319',
'',
'',
'RSV',
'2022JB',
'Unassigned'
);INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'RSV-2022JB-03',
'P220614795',
'',
'',
'RSV',
'2022JB',
'Unassigned'
);INSERT INTO `office_duty`
(`Officer_Key`,
`Devotee_Key`,
`Duty_Type`,
`Duty_Position_Key`,
`Duty_Location_Key`,
`Duty_Event`,
`Duty_Status`)
VALUES
(
'RSV-2022JB-04',
'P18949345',
'',
'',
'RSV',
'2022JB',
'Unassigned'
);

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Adding values for the asset list table
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-ACCO-I', 'KDMS.addaccommodationI','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-ACCO-II', 'KDMS.addaccommodationII','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-DVT-I', 'KDMS.adddevoteeI','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-SEVA-I', 'KDMS.addsevaI','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-SEVA-II', 'KDMS.addsevaII','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-DSBRD', 'KDMS.dashboard','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-DVT-SCR', 'KDMS.devoteesearchresult','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-DVT-DSP', 'KDMS.displaydevotees','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-PRT_ID', 'KDMS.printid','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-PRT-CRD', 'KDMS.rptcardprint','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-PRT-CDS', 'KDMS.rptcardsprint','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-AMT-I', 'KDMS.upsertamenityI','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-AMT-II', 'KDMS.upsertamenityII','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-EVNT-I', 'KDMS.upserteventI','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KD-EVNT-II', 'KDMS.upserteventII','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KR-DSBRD', 'KMREPORTS.dashboard','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KR-R-ACCO', 'KMREPORTS.rptacco','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KR-R-ATTN', 'KMREPORTS.rptattendanceReport','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KR-R-DUTY', 'KMREPORTS.rptdutyreport','anil', NOW());
INSERT INTO `kdms2022`.`asset_list` (`asset_key`,`asset_name`,`asset_updated_by`,`asset_update_date_time`) VALUES ('KR-R-OFDT', 'KMREPORTS.rptofficeduty','anil', NOW());

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // Adding initial values for the user master and user access tables
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
INSERT INTO `kdms_gold_2022`.`user_master` VALUES ('admin', 'Admin - Special User', 'admin@gmail.com', 'admin', '999-999-9999', 'ADMIN');
INSERT INTO `kdms_gold_2022`.`user_access`(`user_role_key`,`asset_key`,`access_value`,`access_updated_by`,`access_update_date_time`) SELECT 'ADMIN', asset_key, 'ALL','anil',NOW() from asset_list;
INSERT INTO `kdms2022`.`user_access`(`user_role_key`,`asset_key`,`access_value`,`access_updated_by`,`access_update_date_time`) SELECT 'SPRUSR', asset_key, 'ALL','anil',NOW() from asset_list WHERE asset_key like 'KD-%';
INSERT INTO `kdms_gold_2022`.`user_master` (`User_Key`, `User_Name`, `User_Email`, `User_Password`, `User_Phone`, `User_Role`) VALUES ('mgmt', 'Management User', 'Management User', 'mgmt', '888-888-8888', 'MGMTUSR');
INSERT INTO `kdms2022`.`user_access`(`user_role_key`,`asset_key`,`access_value`,`access_updated_by`,`access_update_date_time`) SELECT 'MGMTUSR', asset_key, 'ALL','anil',NOW() from asset_list WHERE asset_key like 'KR-%';
INSERT INTO `kdms2023`.`user_master` VALUES ('support', 'Technology Support User', 'kainchi_tech_support@gmail.com', 'support', '999-999-9999', 'SUPPORT');
INSERT INTO `kdms2023`.`user_access`(`user_role_key`,`asset_key`,`access_value`,`access_updated_by`,`access_update_date_time`) SELECT 'SUPPORT', asset_key, 'ALL','anil',NOW() from asset_list;

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // archive office duty data and setup office duty initial record for the next event
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
insert into office_duty_archive select * from office_duty;
update office_duty_archive set duty_status = 'Completed';
UPDATE  kdms2023.office_duty set duty_event = '2023JB'

-- ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
-- // archive seva availability data 
-- //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
insert into seva_availability_archive 
select seva_id, seva_event, count(devotee_key) as assigned_count, '2022-06-16 15:00:00' as archieval_update_date_time , 'Script' as Archived_by from devotee_seva  
where seva_id in (select seva_id from seva_master)
group by seva_id, seva_event