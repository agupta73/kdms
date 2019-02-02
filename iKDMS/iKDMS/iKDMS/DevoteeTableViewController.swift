//
//  DevoteeTableViewController.swift
//  iKDMS
//
//  Created by Gupta, Anil on 1/25/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import UIKit
import os.log

class DevoteeTableViewController: UITableViewController {
    
    //MARK: Properties
    
    var devotees = [Devotee]()
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
        // Use the edit button item provided by the table view controller.
        navigationItem.leftBarButtonItem = editButtonItem
        navigationItem.leftBarButtonItem?.title = "Print Card"
        
        LoadSampleDevoteeRecords()
        LoadDevoteeRecords()
        
        // Uncomment the following line to preserve selection between presentations
        // self.clearsSelectionOnViewWillAppear = false
        
        // Uncomment the following line to display an Edit button in the navigation bar for this view controller.
        // self.navigationItem.rightBarButtonItem = self.editButtonItem
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    // MARK: - Table view data source
    
    override func numberOfSections(in tableView: UITableView) -> Int {
        return 1
    }
    
    override func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return devotees.count
    }
    
    
    override func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        // Table view cells are reused and should be dequeued using a cell identifier.
        let cellIdentifier = "DevoteeTableViewCell"
        
        guard let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? DevoteeTableViewCell  else {
            fatalError("The dequeued cell is not an instance of DevoteeTableViewCell.")
        }
        
        // Fetches the appropriate meal for the data source layout.
        let devotee = devotees[indexPath.row]
        
        cell.lblName.text = devotee.firstName! + " " + devotee.lastName!
        cell.imagePhoto.image = devotee.devoteePhoto
        cell.lblStation.text = devotee.devoteeStation!
        cell.lblDevoteeKey.text = devotee.devoteeKey
        cell.lblAccommodation.text = devotee.devoteeAccoId!
        
        return cell
    }
    
    
    /*
     // Override to support conditional editing of the table view.
     override func tableView(_ tableView: UITableView, canEditRowAt indexPath: IndexPath) -> Bool {
     // Return false if you do not want the specified item to be editable.
     return true
     }
     */
    
    
    // Override to support editing the table view.
    override func tableView(_ tableView: UITableView, commit editingStyle: UITableViewCellEditingStyle, forRowAt indexPath: IndexPath) {
        if editingStyle == .delete {
            // Delete the row from the data source
            tableView.deleteRows(at: [indexPath], with: .fade)
        } else if editingStyle == .insert {
            // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
        }
    }
    
    
    /*
     // Override to support rearranging the table view.
     override func tableView(_ tableView: UITableView, moveRowAt fromIndexPath: IndexPath, to: IndexPath) {
     
     }
     */
    
    /*
     // Override to support conditional rearranging of the table view.
     override func tableView(_ tableView: UITableView, canMoveRowAt indexPath: IndexPath) -> Bool {
     // Return false if you do not want the item to be re-orderable.
     return true
     }
     */
    
    
    // MARK: - Navigation
    
    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        // Get the new view controller using segue.destinationViewController.
        // Pass the selected object to the new view controller.
        super.prepare(for: segue, sender: sender)
        
        switch(segue.identifier ?? "") {
        case "AddDevotee":
            os_log("Adding a new devotee.", log: OSLog.default, type: .debug)
            
        case "ShowDetail":
            guard let devoteeDetailViewController = segue.destination as? DevoteeViewController else {
                fatalError("Unexpected destination: \(segue.destination)")
            }
            
            guard let selectedDevoteeCell = sender as? DevoteeTableViewCell else {
                fatalError("Unexpected sender: \(String(describing: sender))")
            }
            
            guard let indexPath = tableView.indexPath(for: selectedDevoteeCell) else {
                fatalError("The selected cell is not being displayed by the table")
            }
            
            let selectedDevotee = devotees[indexPath.row]
            devoteeDetailViewController.devotee = selectedDevotee
        default:
            fatalError("Unexpected Segue Identifier; \(String(describing: segue.identifier))")
        }
    }
    
    
    //MARK: Private methods
    private func LoadSampleDevoteeRecords(){
        let photo1 = UIImage(named:"Devotee1")
        let photo2 = UIImage(named:"Devotee2")
        let photo3 = UIImage(named:"Devotee3")
        let image1 = UIImage(named:"ID1")
        let image2 = UIImage(named:"ID2")
        let image3 = UIImage(named:"ID3")
        
        guard let devotee1 = Devotee(firstName: "firstName1", lastName: "lastName1", devoteeKey: "P18111759", devoteeType: "devoteeType1", devoteeIdType: "devoteeIdType", devoteeIdNumber: "devoteeIdNumber1",  devoteeStation: "devoteeStation1", devoteePhone: "devoteePhone1", devoteeRemarks: "devoteeRemarks", devoteeAccoId: "devoteeAccoID", devoteePhoto: photo1, devoteeIdImage:image1) else {
            fatalError("Unable to instantiate devotee 1")
        }
        
        guard let devotee2 = Devotee(firstName: "firstName2", lastName: "lastName2", devoteeKey: "P181120237", devoteeType: "devoteeType2", devoteeIdType: "devoteeIdType2", devoteeIdNumber: "devoteeIdNumber2",  devoteeStation: "devoteeStation2", devoteePhone: "devoteePhone2", devoteeRemarks: "devoteeRemarks2", devoteeAccoId: "devoteeAccoID2", devoteePhoto: photo2, devoteeIdImage:image2) else {
            fatalError("Unable to instantiate devotee 2")
        }
        
        guard let devotee3 = Devotee(firstName: "firstName3", lastName: "lastName3", devoteeKey: "P181029893", devoteeType: "devoteeType3", devoteeIdType: "devoteeIdType3", devoteeIdNumber: "devoteeIdNumber3",  devoteeStation: "devoteeStation3", devoteePhone: "devoteePhone3", devoteeRemarks: "devoteeRemarks3", devoteeAccoId: "devoteeAccoID3", devoteePhoto: photo3, devoteeIdImage:image3) else {
            fatalError("Unable to instantiate devotee 3")
        }
        
        devotees += [devotee1, devotee2, devotee3]
        
    }
    
    private func LoadDevoteeRecords() {
        
    }
    
    //MARK: Action
    @IBAction func unwindToDevoteeList(sender: UIStoryboardSegue) {
        
        if let sourceViewController = sender.source as? DevoteeViewController, let devotee = sourceViewController.devotee {
            if let selectedIndexPath = tableView.indexPathForSelectedRow {
                devotees[selectedIndexPath.row] = devotee
                tableView.reloadRows(at: [selectedIndexPath], with: .none)
            }
            else {
                
                // Add a new devotee record
                let newIndexPath = IndexPath(row: devotees.count, section: 0)
                devotees.append(devotee)
                tableView.insertRows(at: [newIndexPath], with: .automatic)
                
            }
        }
    }
}
