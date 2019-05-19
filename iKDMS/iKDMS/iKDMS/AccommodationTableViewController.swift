//
//  AccommodationTableViewController.swift
//  iKDMS
//
//  Created by Gupta, Anil on 5/17/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import UIKit
import Alamofire

class AccommodationTableViewController: UITableViewController {

    override func viewDidLoad() {
         //tableView.register(UINib.init(nibName: "AccommodationTableViewCell", bundle: nil), forCellReuseIdentifier: "AccommodationTableViewCell")
        super.viewDidLoad()
        loadAccommodations()
    }

    
    //MARK: Properties
    var accommodations = [Accommodation]()
    
    // MARK: - Table view data source

    override func numberOfSections(in tableView: UITableView) -> Int {
        return 1
    }

    override func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return accommodations.count
    }

    
    //MARK: Private Methods
    
    private func loadAccommodations() {
        guard let acco1 = Accommodation(accommodationName: "Accommodation One", availableCount: "100", occupiedCount: "10") else {
            fatalError("Unable to instentiate Accommodation")
        }
        
        guard let acco2 = Accommodation(accommodationName: "Accommodation Two", availableCount: "1000", occupiedCount: "20") else {
            fatalError("Unable to instentiate Accommodation")
        }
        
        guard let acco3 = Accommodation(accommodationName: "Accommodation Three", availableCount: "900", occupiedCount: "200") else {
            fatalError("Unable to instentiate Accommodation")
        }
        accommodations += [acco1, acco2, acco3]
    }
    
    
    override func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "AccomodationTableViewCell"
        guard let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? AccommodationTableViewCell  else {
            fatalError("The dequeued cell is not an instance of MealTableViewCell.")
        }
        let accommodation = accommodations[indexPath.row]
        
        cell.lblAccoName.text = accommodation.accommodationName
        cell.lblAvailableCount.text = accommodation.availableCount
        cell.lblOccupiedCount.text = accommodation.occupiedCount
        
        return cell
    }
 

    /*
    // Override to support conditional editing of the table view.
    override func tableView(_ tableView: UITableView, canEditRowAt indexPath: IndexPath) -> Bool {
        // Return false if you do not want the specified item to be editable.
        return true
    }
    */

    /*
    // Override to support editing the table view.
    override func tableView(_ tableView: UITableView, commit editingStyle: UITableViewCell.EditingStyle, forRowAt indexPath: IndexPath) {
        if editingStyle == .delete {
            // Delete the row from the data source
            tableView.deleteRows(at: [indexPath], with: .fade)
        } else if editingStyle == .insert {
            // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
        }    
    }
    */

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

    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        // Get the new view controller using segue.destination.
        // Pass the selected object to the new view controller.
    }
    */

}
