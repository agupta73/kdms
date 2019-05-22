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
        loadSummary()
    }

    
    //MARK: Properties
    var accommodations = [Accommodation]()
    var summaryList = [Accommodation]()
    // MARK: - Table view data source

    override func numberOfSections(in tableView: UITableView) -> Int {
        return 2
    }

    override func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if section == 0 {
            return accommodations.count
        }
        return summaryList.count
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
    
    private func loadSummary() {
        guard let acco1 = Accommodation(accommodationName: "Summay One", availableCount: "100", occupiedCount: "10") else {
            fatalError("Unable to instentiate Accommodation")
        }
        
        guard let acco2 = Accommodation(accommodationName: "Accommodation Two", availableCount: "1000", occupiedCount: "20") else {
            fatalError("Unable to instentiate Accommodation")
        }
        
        guard let acco3 = Accommodation(accommodationName: "Accommodation Three", availableCount: "900", occupiedCount: "200") else {
            fatalError("Unable to instentiate Accommodation")
        }
        summaryList += [acco1, acco2, acco3]
    }
    
    
    override func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "AccomodationTableViewCell"
        guard let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? AccommodationTableViewCell  else {
            fatalError("The dequeued cell is not an instance of MealTableViewCell.")
        }
        if indexPath.section == 0 {
            let accommodation = accommodations[indexPath.row]
            cell.lblAccoName.text = accommodation.accommodationName
            cell.lblAvailableCount.text = accommodation.availableCount
            cell.lblOccupiedCount.text = accommodation.occupiedCount
            
            return cell
        } else {
            let accommodation = summaryList[indexPath.row]
            cell.lblAccoName.text = accommodation.accommodationName
            cell.lblAvailableCount.text = accommodation.availableCount
            cell.lblOccupiedCount.text = accommodation.occupiedCount
            
            return cell
        }
    }
 
}
