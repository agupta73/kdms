-- Optional: enforce idempotent print_log at the database layer (one row per devotee + event + day).
-- Run only after deduplicating existing rows, e.g.:
--   DELETE pl1 FROM print_log pl1
--   INNER JOIN print_log pl2
--     ON pl1.Devotee_Key = pl2.Devotee_Key AND pl1.Event_Id = pl2.Event_Id
--     AND DATE(pl1.Print_Date_Time) = DATE(pl2.Print_Date_Time)
--     AND pl1.Print_Date_Time > pl2.Print_Date_Time;
--
-- Application code (includes/PrintLog.php) already uses INSERT ... WHERE NOT EXISTS for the same rule.

ALTER TABLE print_log
  ADD COLUMN Print_Day DATE GENERATED ALWAYS AS (CAST(Print_Date_Time AS DATE)) STORED,
  ADD UNIQUE KEY uq_print_log_devotee_event_day (Devotee_Key, Event_Id, Print_Day);
