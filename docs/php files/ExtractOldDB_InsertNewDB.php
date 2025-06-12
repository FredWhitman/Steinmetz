<?php
try {
    $sourceDb = new PDO("mysql:host=source_host;dbname=source_db;charset=utf8mb4", "source_user", "source_pass");
    $destDb = new PDO("mysql:host=dest_host;dbname=dest_db;charset=utf8mb4", "dest_user", "dest_pass");

    // Set PDO to throw exceptions on errors
    $sourceDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $destDb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Specify the target year for data transfer
    $targetYear = 2025; // Change this dynamically if needed

    // Begin transaction
    $destDb->beginTransaction();

    // Extract data from productionlogs table filtered by year
    $prodStmt = $sourceDb->prepare("SELECT * FROM productionlogs WHERE YEAR(ProductionDate) = :year");
    $prodStmt->execute([':year' => $targetYear]);

    while ($row = $prodStmt->fetch(PDO::FETCH_ASSOC)) {
        // Insert into productionlogs table in destination DB
        $insertProd = "INSERT INTO productionlogs (productID, prodDate, runStatus, prevProdLogID, runLogID, matLogID, tempLogID, pressCounter, startUpRejects, qaRejects, purgeLbs, Comments) VALUES (:productID, :prodDate, :runStatus, :prevProdLogID, :runLogID, :matLogID, :tempLogID, :pressCounter, :startUpRejects, :qaRejects, :purgeLbs, :Comments)";
        $stmt = $destDb->prepare($insertProd);
        $stmt->execute([
            ':productID' => $row['ProductID'],
            ':prodDate' => $row['ProductionDate'],
            ':runStatus' => $row['ProductionRun'],
            ':prevProdLogID' => $row['PreviousProductionID'],
            ':runLogID' => null,  
            ':matLogID' => null,   
            ':tempLogID' => null,  
            ':pressCounter' => $row['PressCounter'],
            ':startUpRejects' => $row['Rejects'],
            ':qaRejects' => 0, 
            ':purgeLbs' => 0.0, 
            ':Comments' => $row['Comments']
        ]);
        $prodLogID = $destDb->lastInsertId();

        // Insert into templog table
        $insertTemp = "INSERT INTO templog (prodLogID, bigDryerTemp, bigDryerDew, pressDryerTemp, pressDryerDew, t1, t2, t3, t4, m1, m2, m3, m4, m5, m6, m7, chillerTemp, moldTemp) VALUES (:prodLogID, :bigDryerTemp, :bigDryerDew, :pressDryerTemp, :pressDryerDew, :t1, :t2, :t3, :t4, :m1, :m2, :m3, :m4, :m5, :m6, :m7, :chillerTemp, :moldTemp)";
        $stmt = $destDb->prepare($insertTemp);
        $stmt->execute([
            ':prodLogID' => $prodLogID,
            ':bigDryerTemp' => $row['BigDryerTemp'],
            ':bigDryerDew' => $row['BigDryerDew'],
            ':pressDryerTemp' => $row['PressDryerTemp'],
            ':pressDryerDew' => $row['PressDryerDew'],
            ':t1' => $row['T1'], ':t2' => $row['T2'], ':t3' => $row['T3'], ':t4' => $row['T4'],
            ':m1' => $row['M1'], ':m2' => $row['M2'], ':m3' => $row['M3'], ':m4' => $row['M4'],
            ':m5' => $row['M5'], ':m6' => $row['M6'], ':m7' => $row['M7'],
            ':chillerTemp' => $row['ChillerTemp'], ':moldTemp' => $row['MoldTemp']
        ]);

        // Insert into materiallog table
        $insertMat = "INSERT INTO materiallog (prodLogID, mat1, matUsed1, mat2, matUsed2, mat3, matUsed3, mat4, matUsed4) VALUES (:prodLogID, :mat1, :matUsed1, :mat2, :matUsed2, :mat3, :matUsed3, :mat4, :matUsed4)";
        $stmt = $destDb->prepare($insertMat);
        $stmt->execute([
            ':prodLogID' => $prodLogID,
            ':mat1' => $row['Hopper1Mat'], ':matUsed1' => $row['Hopper1Lbs'],
            ':mat2' => $row['Hopper2Mat'], ':matUsed2' => $row['Hopper2Lbs'],
            ':mat3' => $row['Hopper3Mat'], ':matUsed3' => $row['Hopper3Lbs'],
            ':mat4' => $row['Hopper4Mat'], ':matUsed4' => $row['Hopper4Lbs']
        ]);
    }

    // Extract data from productionruns table filtered by year
    $runStmt = $sourceDb->prepare("SELECT * FROM productionruns WHERE YEAR(StartDate) = :year");
    $runStmt->execute([':year' => $targetYear]);

    while ($row = $runStmt->fetch(PDO::FETCH_ASSOC)) {
        // Insert into prodrunlog table in destination DB
        $insertRun = "INSERT INTO prodrunlog (productID, startDate, endDate, mat1Lbs, mat2Lbs, mat3Lbs, mat4Lbs, partsProduced, startupRejects, qaRejects, purgeLbs, runComplete) VALUES (:productID, :startDate, :endDate, :mat1Lbs, :mat2Lbs, :mat3Lbs, :mat4Lbs, :partsProduced, :startupRejects, :qaRejects, :purgeLbs, :runComplete)";
        $stmt = $destDb->prepare($insertRun);
        $stmt->execute([
            ':productID' => $row['ProductID'],
            ':startDate' => $row['StartDate'],
            ':endDate' => $row['EndDate'],
            ':mat1Lbs' => $row['MatHopper1'], ':mat2Lbs' => $row['MatHopper2'],
            ':mat3Lbs' => $row['MatHopper3'], ':mat4Lbs' => $row['MatHopper4'],
            ':partsProduced' => $row['TotalPartsMolded'],
            ':startupRejects' => $row['TotalRejects'], ':qaRejects' => 0, ':purgeLbs' => 0.0,
            ':runComplete' => $row['Completed']
        ]);
    }

    // Commit transaction
    $destDb->commit();
    echo "Data successfully transferred for year $targetYear!";
} catch (Exception $e) {
    // Roll back transaction on error
    $destDb->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
