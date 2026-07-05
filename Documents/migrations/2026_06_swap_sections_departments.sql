-- Migration: Swap Sections <-> Departments hierarchy
-- Purpose: Convert current hierarchy (branches -> sections -> departments)
-- to (branches -> departments -> sections) by renaming tables and swapping FK values.
-- IMPORTANT: BACKUP your database before running this script.

SET FOREIGN_KEY_CHECKS=0;

-- 1) Rename tables to swap roles
RENAME TABLE sections TO tmp_sections, departments TO sections, tmp_sections TO departments;

-- 2) Rename column in new `sections` (previously departments): `section_id` -> `department_id`
ALTER TABLE sections CHANGE COLUMN section_id department_id INT DEFAULT NULL;

-- 3) Update departments table: ensure it has branch_id (it already has, from old sections)
-- (no action required if departments already has branch_id column)

-- 4) Swap inventory_items.section_id <-> inventory_items.department_id values
ALTER TABLE inventory_items DROP FOREIGN KEY inventory_items_ibfk_2; -- may vary; adjust if needed
ALTER TABLE inventory_items DROP FOREIGN KEY inventory_items_ibfk_3; -- may vary; adjust if needed

UPDATE inventory_items SET @tmp = section_id, section_id = department_id, department_id = @tmp;

-- 5) Swap users.section_id <-> users.department_id (if users use these fields similarly)
UPDATE users SET @tmp = section_id, section_id = department_id, department_id = @tmp;

-- 6) Recreate foreign keys for inventory_items
ALTER TABLE inventory_items
    DROP FOREIGN KEY IF EXISTS fk_inventory_section,
    DROP FOREIGN KEY IF EXISTS fk_inventory_department;

ALTER TABLE inventory_items
    ADD CONSTRAINT fk_inventory_department FOREIGN KEY (department_id) REFERENCES departments(id),
    ADD CONSTRAINT fk_inventory_section FOREIGN KEY (section_id) REFERENCES sections(id);

-- 7) Update departments table FK references (if any referencing users or sections need fixups)
-- 8) Re-enable FK checks
SET FOREIGN_KEY_CHECKS=1;

-- NOTE: The actual foreign key constraint names may differ. Inspect your DB engine for correct names
-- and adjust DROP/ADD statements accordingly. Test on a backup copy first.
