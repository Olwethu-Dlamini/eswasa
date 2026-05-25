-- ESWASA — seed team members on production
-- The main migration migrated board → team as council, but on this
-- server eswasa_board_members was empty so the team table stayed empty.
-- This file inserts the canonical 9 members directly.
-- Idempotent: INSERT IGNORE / ON DUPLICATE KEY skips rows that already exist.

INSERT INTO eswasa_team_members
    (id, name,                       role,                         photo,                            section,      sort_order, is_vacant)
VALUES
    (1, 'Mr. Ncamiso K. Mhlanga',     'Executive Director',         'Ncamiso.jpg',                    'management',  1, 0),
    (2, 'Ms. Dumsile Masina',         'CFO',                        'masina.jpg',                     'management',  2, 0),
    (3, 'Mr. Phillip G. Mndawe',      'Technical Manager',          'philip.jpg',                     'management',  3, 0),
    (4, 'Vacant',                     'Quality Assurance Manager',  'management/director_finance.jpg','management',  4, 1),
    (5, 'Mrs. Dumile Sibandze',       'Chairperson',                'dumile.png',                     'council',     1, 0),
    (6, 'Ms. Cebile Nhlabatsi',       'Council Member',             'cebile.jpg',                     'council',     2, 0),
    (7, 'Ms. Nompumelelo Dladla',     'Council Member',             'Dladla.jpg',                     'council',     3, 0),
    (8, 'Ms. Tania Fyfe',             'Council Member',             'Tania.jpg',                      'council',     4, 0),
    (9, 'Ms. Sipholesihle Sukati',    'Council Member',             'sukati.png',                     'council',     5, 0)
ON DUPLICATE KEY UPDATE
    name       = VALUES(name),
    role       = VALUES(role),
    photo      = VALUES(photo),
    section    = VALUES(section),
    sort_order = VALUES(sort_order),
    is_vacant  = VALUES(is_vacant);

-- Seed the editable section titles used on Meetourteam.php (idempotent).
INSERT INTO page_content (page_key, content) VALUES
    ('team_section_main_title',      'Our Council and Management'),
    ('team_section_council_title',   'Members of the Council'),
    ('team_section_executive_title', 'Executive Team')
ON DUPLICATE KEY UPDATE content = VALUES(content);

-- Verify
SELECT COUNT(*) AS total_after_seed FROM eswasa_team_members;
SELECT id, name, role, section, sort_order, is_vacant
  FROM eswasa_team_members
  ORDER BY section, sort_order;
