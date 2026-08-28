USE impmartinez;

ALTER TABLE equipos 
ADD COLUMN costo_final DECIMAL(10,2) NULL,
ADD COLUMN firma_entrega LONGBLOB NULL,
ADD COLUMN fecha_conformidad_entrega DATETIME NULL;
