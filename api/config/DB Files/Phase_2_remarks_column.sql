-- Phase 2 — widen devotee_remarks.remark for dedup JSON audit trails
ALTER TABLE devotee_remarks
    MODIFY COLUMN remark TEXT NULL;
