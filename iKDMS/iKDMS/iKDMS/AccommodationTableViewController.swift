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
    var summaryList = [summaryCount]()
    struct summaryCount: Codable {
        var SummaryID: String
        var SummaryCount: String
    }
    var selectedRowIndex: Int = 0
    
    // MARK: - Table view data source

    override func numberOfSections(in tableView: UITableView) -> Int {
        return 2
    }

    override func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        if section == 1 {
            return accommodations.count
        }
        return summaryList.count
    }

    
    //MARK: Private Methods
    
    @objc private func loadSummary()  {
        summaryList.removeAll()
        let urlString = "http://FSCAM0RLHV2R.local/KDMS/api/getReport.php?type=DevoteeCount"
        var i = 0
        Alamofire.request(urlString).responseJSON { response in
            if let json = response.result.value {
                let parsedData = json as! NSArray
                
                for id in parsedData {
                    let parsedSummary = id as! NSDictionary
                    switch (i) {
                    case 0:
                        self.summaryList.append(summaryCount(SummaryID: "Total Space Allocated", SummaryCount: parsedSummary.object(forKey: "SpaceOccupiedOrDevoteesPresent") as! String))
                        break
                        
                    case 1:
                        //self.summaryList.append(summaryCount(SummaryID: "RegisteredDevoteesIncludingLocals", SummaryCount: parsedSummary.object(forKey: "RegisteredDevoteesIncludingLocals") as! String))
                        break
                        
                    case 2:
                        self.summaryList.append(summaryCount(SummaryID: "Total Spaces Available", SummaryCount: parsedSummary.object(forKey: "AvailableSpaces") as! String))
                        break
                        
                    case 3:
                        self.summaryList.append(summaryCount(SummaryID: "Total Spaces Reserved", SummaryCount: parsedSummary.object(forKey: "ReservedSpaces") as! String))
                        break
                        
                    case 4:
                        self.summaryList.append(summaryCount(SummaryID: "Devotees With Own Arrangements", SummaryCount: parsedSummary.object(forKey: "DevoteesWithOwnArrangements") as! String))
                        break
                        
                    default:
                        
                        break
                    }
                    i = i + 1
                }
                self.tableView.reloadData()
            }
        }
        //self.refreshControl?.endRefreshing()
    }
    
    @objc private func loadAccommodations()  {
        accommodations.removeAll()
        var urlString: String
        switch selectedRowIndex {
        case 0:
            urlString = "http://FSCAM0RLHV2R.local/KDMS/api/getReport.php?type=AccoCount&AccoType=Reserved"
        case 1:
            urlString = "http://FSCAM0RLHV2R.local/KDMS/api/getReport.php?type=AccoCount&AccoType=Reserved"
        case 2:
            urlString = "http://FSCAM0RLHV2R.local/KDMS/api/getReport.php?type=AccoCount&AccoType=Reserved"
        default:
            urlString = "http://FSCAM0RLHV2R.local/KDMS/api/getReport.php?type=AccoCount&AccoType=All"
        }
        
        Alamofire.request(urlString).responseJSON { response in
            if let json = response.result.value {
                let parsedData = json as! NSArray
                
                for id in parsedData {
                    let parsedAcco = id as! NSDictionary
                    var accoName = parsedAcco.object(forKey: "accomodation_name")  as? String
                    accoName = accoName?.replacingOccurrences(of: "+", with: " ")
                    self.accommodations.append(Accommodation(accommodationName: accoName!,
                                               availableCount: parsedAcco.object(forKey: "available_count") as? String,
                                               occupiedCount: parsedAcco.object(forKey: "occupied_count") as? String,
                                               accomodationKey: parsedAcco.object(forKey: "accomodation_key") as? String,
                                               reservedCount: parsedAcco.object(forKey: "reserved_count") as? String,
                                               allocatedCount: parsedAcco.object(forKey: "allocated_count") as? String,
                                               outOfAvailabilityCount: parsedAcco.object(forKey: "Out_of_Availability_Count") as? String ?? "")!)
                        
                        
                        /* (firstName: parsedAcco.object(forKey: "devotee_first_name") as? String,
                                                 lastName: parsedAcco.object(forKey: "devotee_last_name") as? String,
                                                 devoteeKey: (parsedAcco.object(forKey: "devotee_key")  as? String)!,
                                                 devoteeType: parsedAcco.object(forKey: "devotee_type") as? String,
                                                 devoteeIdType: parsedAcco.object(forKey: "devotee_id_type") as? String,
                                                 devoteeIdNumber: parsedAcco.object(forKey: "devotee_id_number") as? String,
                                                 devoteeStation: parsedAcco.object(forKey: "devotee_station")  as? String,
                                                 devoteePhone: parsedAcco.object(forKey: "devotee_cell_phone_number")  as? String,
                                                 devoteeRemarks: parsedAcco.object(forKey: "devotee_remarks")  as? String,
                                                 devoteeAccoId: parsedAcco.object(forKey: "accomodation_key")  as? String,
                                                 devoteeAccoName: accoName,
                                                 devoteePhoto: self.loadImage(imageData: (parsedDevotee.object(forKey: "Devotee_Photo")  as? String) ?? ""),
                                                 devoteeIdImage: self.loadImage(imageData: (parsedDevotee.object(forKey: "Devotee_ID_Image")  as? String) ?? ""))!) */
                    
                }
                self.tableView.reloadData()
            }
        }
       // self.refreshControl?.endRefreshing()
    }
    
   /* private func loadAccommodations_old() {
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
    }*/
    
    override func tableView(_ tableView: UITableView, titleForHeaderInSection section: Int) -> String? {
        if section == 0 {
            return "Summary Counts: Click on row to see details"
        } else {
            return "All Accommodations"
        }
        
    }
    
    override func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "AccomodationTableViewCell"
        
        let lblOccupied = "Occupied: "
        let lblAvailable = "Available: "
        
        guard let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? AccommodationTableViewCell  else {
            fatalError("The dequeued cell is not an instance of MealTableViewCell.")
        }
        if indexPath.section == 1 {
            let accommodation = accommodations[indexPath.row]
            cell.lblAccoName.text = accommodation.accommodationName
            cell.lblAvailableCount.text = lblAvailable +  accommodation.availableCount!
            cell.lblOccupiedCount.text = lblOccupied + accommodation.occupiedCount!
            tableView.rowHeight = 50
            cell.backgroundColor = UIColor.clear
            return cell
        } else {
            let summaryCount = summaryList[indexPath.row]
            cell.lblAccoName.text = summaryCount.SummaryID + ": " + summaryCount.SummaryCount
            cell.lblAvailableCount.text = ""
            cell.lblOccupiedCount.text = ""
            tableView.rowHeight = 30
            cell.backgroundColor = UIColor.lightGray
            return cell
        }
    }
 
}
