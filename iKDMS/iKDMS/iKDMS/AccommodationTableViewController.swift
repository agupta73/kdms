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
    var filterAcco: String = ""
    
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
        let urlString = "http://192.168.0.103/KDMS/api/getReport.php?type=DevoteeCount"
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
        switch filterAcco {
        case "Total Space Allocated":
            urlString = "http://192.168.0.103/KDMS/api/getReport.php?type=AccoCount&accoType=Occupied"
            break
        case "Total Spaces Available":
            urlString = "http://192.168.0.103/KDMS/api/getReport.php?type=AccoCount&accoType=Available"
            break
        case "Total Spaces Reserved":
            urlString = "http://192.168.0.103/KDMS/api/getReport.php?type=AccoCount&accoType=Reserved"
            break
        case "Devotees With Own Arrangements":
            urlString = "http://192.168.0.103/KDMS/api/getReport.php?type=AccoCount&accoType=All"
            break
        default:
            urlString = "http://192.168.0.103/KDMS/api/getReport.php?type=AccoCount&accoType=All"
            break
        }
        
        print(urlString)
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
                }
                self.tableView.reloadSections([1], with: .none)
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
            switch filterAcco {
            case "Total Space Allocated":
                return "Allocated Accommodations"
            break
            case "Total Spaces Available":
                return "Available Accommodations"
            break
            case "Total Spaces Reserved":
                return "Reserved Accommodations"
            break
            case "Devotees With Own Arrangements":
                return "All Accommodations"
            break
            default:
                return "All Accommodations"
            break
            }
        }
        
    }
    
    override func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cellIdentifier = "AccomodationTableViewCell"
        var lblOccupied: String
        let lblAvailable = "Available: "
        if filterAcco == "Total Spaces Reserved" {
            lblOccupied = "Reserved: "
        } else {
            lblOccupied = "Allocated: "
        }
        
        guard let cell = tableView.dequeueReusableCell(withIdentifier: cellIdentifier, for: indexPath) as? AccommodationTableViewCell  else {
            fatalError("The dequeued cell is not an instance of MealTableViewCell.")
        }
        if indexPath.section == 1 {
            let accommodation = accommodations[indexPath.row]
            cell.lblAccoName.text = accommodation.accommodationName
            cell.lblAvailableCount.text = lblAvailable +  accommodation.availableCount!
            if filterAcco == "Total Spaces Reserved" {
                cell.lblOccupiedCount.text = lblOccupied + accommodation.reservedCount!
            } else {
                cell.lblOccupiedCount.text = lblOccupied + accommodation.allocatedCount!
            }
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
    
    override func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if indexPath.section == 0 {
            let summaryCount = summaryList[indexPath.row]
            filterAcco = summaryCount.SummaryID
            loadAccommodations()
            //self.tableView.reloadSections([1], with: .none)
            if filterAcco == "Devotees With Own Arrangements" {
//                let storyboard = UIStoryboard(name: "Main", bundle: nil)
//                let devoteeViewController = storyboard.instantiateViewController(withIdentifier:"DevoteeViewController") as? DevoteeViewController
//                //devoteeViewController?.devotee  = newDevotee
//                self.navigationController?.pushViewController(devoteeViewController!, animated: true)
            }
            
            
        }
    }
 
}
