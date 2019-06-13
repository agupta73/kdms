//
//  DevoteeTableViewController.swift
//  iKDMS
//
//  Created by Gupta, Anil on 1/25/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import UIKit
import os.log
import Alamofire

class DevoteeTableViewController: UITableViewController {
    
    //MARK: Properties
    
    var devotees = [Devotee]()
    var selectedTabIndex: Int = 0
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        self.refreshControl?.addTarget(self, action: #selector(DevoteeTableViewController.LoadDevoteeRecords), for: UIControlEvents.valueChanged)
    }
    
    override func viewDidAppear(_ animated: Bool) {
        super.viewDidAppear(animated)
        selectedTabIndex = (tabBarController?.selectedIndex)!
        //tabIndex =  (tabBarController?.selectedIndex)!
        switch selectedTabIndex {
        case 0:
            navigationItem.leftBarButtonItem?.title = "Print Card"
            navigationItem.title = "Print Queue"
        case 1:
            navigationItem.leftBarButtonItem?.title = "Take Photo"
            navigationItem.title = "Photo Missing"
        case 2:
            navigationItem.leftBarButtonItem?.title = "Fill Information"
            navigationItem.title = "Details Missing"
        default:
            navigationItem.leftBarButtonItem?.title = "Invalid Button"
            navigationItem.title = "Inalid Button"
        }
        LoadDevoteeRecords()
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
        let devotee_first_name = devotee.firstName ?? ""
        let devotee_last_name = devotee.lastName ?? ""
        
//        cell.lblName.text = (devotee.firstName ?? "" + " ")// + devotee.lastName ?? "" ?? "") ?? ""
        cell.lblName.text = devotee_first_name + " " + devotee_last_name
        cell.imagePhoto.image = devotee.devoteePhoto
        cell.lblStation.text = devotee.devoteeStation ?? ""
        cell.lblDevoteeKey.text = devotee.devoteeKey ?? ""
        cell.lblAccommodation.text = devotee.devoteeAccoName ?? ""
        
        return cell
    }
    
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
            
            //let selectedDevotee = devotees[indexPath.row]
            //let updatedDevotee = loadDevoteeRecordDetail(passedDevotee: selectedDevotee)
            devoteeDetailViewController.devotee = devotees[indexPath.row] // updatedDevotee
            //print(devoteeDetailViewController.devotee?.devoteeAccoId)
        default:
            fatalError("Unexpected Segue Identifier; \(String(describing: segue.identifier))")
        }
    }
    
    
    //MARK: Private methods
     
    @objc private func LoadDevoteeRecords()  {
        devotees.removeAll()
        var urlString: String
        switch selectedTabIndex {
        case 0:
            urlString = "http://192.168.0.103/KDMS/api/searchDevotee.php?mode=iSET&key=CTP"
        case 1:
            urlString = "http://192.168.0.103/KDMS/api/searchDevotee.php?mode=iSET&key=DWP"
        case 2:
            urlString = "http://192.168.0.103/KDMS/api/searchDevotee.php?mode=iSET&key=PWD"
        default:
            urlString = "http://192.168.0.103/KDMS/api/searchDevotee.php?mode=iSET&key=CTP"
        }
        
        Alamofire.request(urlString).responseJSON { response in
            if let json = response.result.value {
                let parsedData = json as! NSArray
                
                for id in parsedData {
                    let parsedDevotee = id as! NSDictionary
                    var accoName = parsedDevotee.object(forKey: "accomodation_name")  as? String
                    accoName = accoName?.replacingOccurrences(of: "+", with: " ")
                    self.devotees.append(Devotee(firstName: parsedDevotee.object(forKey: "devotee_first_name") as? String,
                                                 lastName: parsedDevotee.object(forKey: "devotee_last_name") as? String,
                                                 devoteeKey: (parsedDevotee.object(forKey: "devotee_key")  as? String)!,
                                                 devoteeType: parsedDevotee.object(forKey: "devotee_type") as? String,
                                                 devoteeIdType: parsedDevotee.object(forKey: "devotee_id_type") as? String,
                                                 devoteeIdNumber: parsedDevotee.object(forKey: "devotee_id_number") as? String,
                                                 devoteeStation: parsedDevotee.object(forKey: "devotee_station")  as? String,
                                                 devoteePhone: parsedDevotee.object(forKey: "devotee_cell_phone_number")  as? String,
                                                 devoteeRemarks: parsedDevotee.object(forKey: "devotee_remarks")  as? String,
                                                 devoteeAccoId: parsedDevotee.object(forKey: "accomodation_key")  as? String,
                                                 devoteeAccoName: accoName,
                                                 devoteePhoto: self.loadImage(imageData: (parsedDevotee.object(forKey: "Devotee_Photo")  as? String) ?? ""),
                                                 devoteeIdImage: self.loadImage(imageData: (parsedDevotee.object(forKey: "Devotee_ID_Image")  as? String) ?? ""))!)
                    self.tableView.reloadData()
                }
            }
        }
        self.refreshControl?.endRefreshing()
    }
    
    
    
    private func loadImage(imageData: String) -> UIImage? {
            let unencodedData = Data(base64Encoded: imageData)
  //          print(imageData)
            let image = UIImage(data: unencodedData!)
            return image
    }
    
    //MARK: Action
    @IBAction func unwindToDevoteeList(sender: UIStoryboardSegue) {
        
        LoadDevoteeRecords()
        // tableView.reloadData()
        /*if let sourceViewController = sender.source as? DevoteeViewController, let devotee = sourceViewController.devotee {
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
        }*/
    }
    
    //MARK: Commented code
    /* @objc func Refresh(sender:AnyObject) {
     LoadDevoteeRecords()
     }
     
     private func loadDevoteeRecordDetail_Old(devotee: Devotee) -> Devotee {
     if(devotee.devoteeKey != "") {
     let urlString = "http://192.168.0.103/KDMS/api/searchDevotee.php?mode=KEY&key=" + devotee.devoteeKey!
     //print(urlString)
     guard let url = URL(string: urlString) else { return devotee }
     let sem = DispatchSemaphore(value: 0)
     URLSession.shared.dataTask(with: url) { (data, response, error) in
     if error != nil {
     print(error!.localizedDescription)
     }
     
     guard let data = data else { return }
     //Implement JSON decoding and parsing
     do {
     //Decode retrived data with JSONDecoder and assing type of Article object
     let parsedData = try JSONDecoder().decode(DevoteeDetailedStructure.self, from: data)
     
     //Get back to the main queue
     DispatchQueue.main.async {
     //self.txtDevoteeKey.text = parsedData.Devotee_Key
     devotee.devoteeRemarks = parsedData.Devotee_Remarks
     devotee.firstName = parsedData.Devotee_First_Name
     devotee.lastName = parsedData.Devotee_Last_Name
     devotee.devoteeType = parsedData.Devotee_Type
     devotee.devoteeIdType = parsedData.Devotee_ID_Type
     devotee.devoteeIdNumber = parsedData.Devotee_ID_Number
     devotee.devoteePhone = parsedData.Devotee_Cell_Phone_Number
     devotee.devoteeStation = parsedData.Devotee_Station
     devotee.devoteeAccoId = parsedData.Accomodation_Key
     
     
     }
     
     } catch let jsonError {
     print(jsonError)
     }
     }.resume()
     sem.wait()
     //DispatchSemaphore.wait(sem, DISPATCH_TIME_FOREVER)
     
     }
     return devotee
     }
     
     private func loadDevoteeRecordDetail(passedDevotee: Devotee) -> Devotee {
     if(passedDevotee.devoteeKey != "") {
     Alamofire.request("http://192.168.0.103/KDMS/api/searchDevotee.php?mode=KEY&key=" + passedDevotee.devoteeKey!).responseJSON { response in
     //print("Request: \(String(describing: response.request))")   // original url request
     //print("Response: \(String(describing: response.response))") // http url response
     //print("Result: \(response.result)")                         // response serialization result
     
     if let json = response.result.value {
     let parsedData = json as! NSDictionary
     //print("JSON: \(json)") // serialized json response
     //print(parsedData.object(forKey: "Devotee_First_Name") ?? "" )
     passedDevotee.devoteeRemarks = parsedData.object(forKey: "Devotee_Remarks") as? String
     passedDevotee.firstName = parsedData.object(forKey: "Devotee_First_Name") as? String
     passedDevotee.lastName = parsedData.object(forKey: "Devotee_Last_Name") as? String
     passedDevotee.devoteeType = parsedData.object(forKey: "Devotee_Type") as? String
     passedDevotee.devoteeIdType = parsedData.object(forKey: "Devotee_ID_Type") as? String
     passedDevotee.devoteeIdNumber = parsedData.object(forKey: "Devotee_ID_Number") as? String
     passedDevotee.devoteePhone = parsedData.object(forKey: "Devotee_Cell_Phone_Number") as? String
     passedDevotee.devoteeStation = parsedData.object(forKey: "Devotee_Station") as? String
     passedDevotee.devoteeAccoId = parsedData.object(forKey: "Accomodation_Key") as? String
     }
     
     /*if let data = response.data, let utf8Text = String(data: data, encoding: .utf8) {
     print("Data: \(utf8Text)") // original server data as UTF8 string
     }*/
     }
     
     }
     return passedDevotee
     }
     
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
     
     private func LoadDevoteeRecords_Old() {
     
     devotees.removeAll()
     var urlString: String
     switch selectedTabIndex {
     case 0:
     urlString = "http://192.168.0.103/KDMS/api/searchDevotee.php?mode=iSET&key=CTP"
     case 1:
     urlString = "http://192.168.0.103/KDMS/api/searchDevotee.php?mode=iSET&key=DWP"
     case 2:
     urlString = "http://192.168.0.103/KDMS/api/searchDevotee.php?mode=iSET&key=PWD"
     default:
     urlString = "http://192.168.0.103/KDMS/api/searchDevotee.php?mode=iSET&key=CTP"
     }
     
     guard let url = URL(string: urlString) else { return }
     
     URLSession.shared.dataTask(with: url) { (data, response, error) in
     if error != nil {
     print(error!.localizedDescription)
     }
     
     guard let data = data else { return }
     //Implement JSON decoding and parsing
     do {
     //Decode retrived data with JSONDecoder and assing type of Article object
     let parsedData = try JSONDecoder().decode([DevoteeStructure].self, from: data)
     
     //Get back to the main queue
     DispatchQueue.main.async {
     //print(parsedData)
     for devoteeRecord in parsedData {
     self.devotees.append(Devotee(firstName: devoteeRecord.Devotee_Name ?? "", lastName: "", devoteeKey: devoteeRecord.devotee_key, devoteeType: "", devoteeIdType: "", devoteeIdNumber: "", devoteeStation: devoteeRecord.devotee_station ?? "", devoteePhone: devoteeRecord.devotee_cell_phone_number ?? "", devoteeRemarks: "", devoteeAccoId: "", devoteePhoto: self.loadImage(imageData: devoteeRecord.Devotee_Photo!), devoteeIdImage: self.loadImage(imageData: devoteeRecord.Devotee_ID_Image ?? ""))!)
     }
     self.tableView.reloadData()
     }
     
     } catch let jsonError {
     print(jsonError)
     }
     }.resume()
     
     self.refreshControl?.endRefreshing()
     }
     */
    //    struct DevoteeStructure: Codable {
    //        var devotee_key: String
    //        var Devotee_Name: String?
    //        var devotee_station: String?
    //        var devotee_cell_phone_number: String?
    //        var Devotee_ID_Image: String?
    //        var Devotee_Photo: String?
    //    }
    //    struct DevoteeDetailedStructure: Codable {
    //        var Devotee_Key: String
    //        var Devotee_Type: String?
    //        var Devotee_First_Name: String?
    //        var Devotee_Last_Name: String?
    //        var Devotee_Gender: String?
    //        var Devotee_ID_Type: String?
    //        var Devotee_ID_Number: String?
    //        var Devotee_Station: String?
    //        var Devotee_Cell_Phone_Number: String?
    //        var Devotee_Status: String?
    //        var Devotee_Remarks: String?
    //        var Devotee_Record_Update_Date_Time: String?
    //        var Devotee_Record_Updated_By: String?
    //        var Devotee_ID_Image: String?
    //        var Devotee_ID_XML: String?
    //        var DID_Devotee_ID_Type: String?
    //        var Photo_type: String?
    //        var Devotee_Photo: String?
    //        var Accomodation_Key: String
    //    }
    
}
