-- Migración para agregar campo tipo a equipos_fotos
USE impmartinez;

ALTER TABLE equipos_fotos 
ADD COLUMN tipo VARCHAR(20) DEFAULT 'general' AFTER orden;

-- Los valores posibles para tipo son:
-- 'anverso' - Foto de la parte frontal del equipo
-- 'reverso' - Foto de la parte trasera del equipo
-- 'general' - Foto general (por defecto)
