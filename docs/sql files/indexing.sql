CREATE INDEX idx_productinventory_productID ON productinventory(productID);
CREATE INDEX idx_products_productID_displayOrder ON products(productID, displayOrder);
CREATE INDEX idx_materialinventory_matPartNumber ON materialinventory(matPartNumber);
CREATE INDEX idx_material_matPartNumber_displayOrder ON material(matPartNumber, displayOrder);

CREATE INDEX idx_pfminventory_partNumber ON pfminventory(partNumber);
CREATE INDEX idx_pfm_partNumber_displayOrder ON pfm(partNumber, displayOrder);