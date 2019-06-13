//
//  DevoteeSearchViewController.swift
//  iKDMS
//
//  Created by Gupta, Anil on 5/24/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import UIKit
import os.log
import Foundation
import Alamofire

class DevoteeSearchViewController: UIViewController, UITextFieldDelegate, UITextViewDelegate, UIImagePickerControllerDelegate, UINavigationControllerDelegate, UIPickerViewDelegate, UIPickerViewDataSource  {

    //MARK: Properties
    
  
    @IBOutlet weak var txtDevoteeKey: UITextField!
    @IBOutlet weak var txtFirstName: UITextField!
    @IBOutlet weak var txtLastName: UITextField!
    @IBOutlet weak var txtIDNumber: UITextField!
    @IBOutlet weak var txtStation: UITextField!
    @IBOutlet weak var txtPhoneNumber: UITextField!
    @IBOutlet weak var txtAccommodation: UITextField!
    @IBOutlet weak var txtRemarks: UITextView!

    var accoDetailValues: [String] = Array()
    var accoIDValues: [String] = Array()
    var devotee: Devotee?
    
    let accoPicker = UIPickerView()
    
    override func viewDidLoad() {
        super.viewDidLoad()

        //Delegate for text fields
        txtFirstName.delegate = self
        txtDevoteeKey.delegate = self
        txtLastName.delegate = self
        txtIDNumber.delegate = self
        txtPhoneNumber.delegate = self
        txtStation.delegate = self
        txtAccommodation.delegate = self
        txtRemarks.delegate = self
        accoPicker.delegate = self
        accoPicker.dataSource = self
        loadMasterData()
    }
    
    
    //MARK: Action
    @IBAction func searchDevotee(_ sender: Any) {
       
    }
    
    @IBAction func Cancel(_ sender: Any) {
        let isPresentingInAddDevoteeMode = presentingViewController is UINavigationController
        
        if isPresentingInAddDevoteeMode {
            dismiss(animated: true, completion: nil)
        }
        else if let owningNavigationController = navigationController{
            owningNavigationController.popViewController(animated: true)
        }
        else {
            fatalError("The DevoteeViewController is not inside a navigation controller.")
        }
    }
    
    //MARK: Private Functions
    private func loadMasterData() {
        
        loadAccommodations(completion: {
            self.txtAccommodation.inputView = self.accoPicker
        })
    }
    private func searchDevoteeDetail(completion: @escaping () -> ()) {
        
        let urlString = "http://192.168.0.103/KDMS/api/searchDevotee.php?mode=KEY&key=" + txtDevoteeKey.text!
        
        Alamofire.request(urlString).responseJSON { response in
            if let json = response.result.value {
                let parsedDevotee = json as! NSDictionary
                var accoName = parsedDevotee.object(forKey: "accomodation_name")  as? String
                accoName = accoName?.replacingOccurrences(of: "+", with: " ")
                self.devotee = Devotee(firstName: parsedDevotee.object(forKey: "Devotee_First_Name") as? String,
                                             lastName: parsedDevotee.object(forKey: "Devotee_Last_Name") as? String,
                                             devoteeKey: (parsedDevotee.object(forKey: "Devotee_Key")  as? String) ?? "",
                                             devoteeType: parsedDevotee.object(forKey: "Devotee_Type") as? String,
                                             devoteeIdType: parsedDevotee.object(forKey: "Devotee_Id_Type") as? String,
                                             devoteeIdNumber: parsedDevotee.object(forKey: "Devotee_Id_Number") as? String,
                                             devoteeStation: parsedDevotee.object(forKey: "Devotee_Station")  as? String,
                                             devoteePhone: parsedDevotee.object(forKey: "Devotee_Cell_Phone_Number")  as? String,
                                             devoteeRemarks: parsedDevotee.object(forKey: "Devotee_Remarks")  as? String,
                                             devoteeAccoId: parsedDevotee.object(forKey: "Accomodation_Key")  as? String,
                                             devoteeAccoName: accoName,
                                             devoteePhoto: self.loadImage(imageData: (parsedDevotee.object(forKey: "Devotee_Photo")  as? String) ?? ""),
                                             devoteeIdImage: self.loadImage(imageData: (parsedDevotee.object(forKey: "Devotee_ID_Image")  as? String) ?? ""))
//                print(self.devotee?.firstName)
            }
            completion()
        }
    }
    
    private func loadImage(imageData: String) -> UIImage? {
        let unencodedData = Data(base64Encoded: imageData)
        //          print(imageData)
        let image = UIImage(data: unencodedData!)
        return image
    }
    
    
    private func loadAccommodations(completion: @escaping () -> ()) {
        
        let urlString = "http://192.168.0.103/KDMS/api/loadoptions.php?option_type=Accommodation"
        
        Alamofire.request(urlString).responseJSON { response in
            if let json = response.result.value {
                let parsedData = json as! NSArray
                
                for id in parsedData {
                    let parsedAcco = id as! NSDictionary
                    var accoName = parsedAcco.object(forKey: "Accomodation_Name")  as? String ?? ""
                    let accoAvail = parsedAcco.object(forKey: "Available_Count")  as? String ?? ""
                    accoName = accoName.replacingOccurrences(of: "+", with: " ")
                    self.accoDetailValues.append(accoName + " - " + accoAvail)
                    self.accoIDValues.append(parsedAcco.object(forKey: "accomodation_key")  as? String ?? ""  )
                }
            }
            completion()
        }
    }
    
    private func getAccommodationValuefromKey(passedKey: String) -> String {
        for i in 0..<self.accoIDValues.count {
            if self.accoIDValues[i] == passedKey {
                return self.accoDetailValues[i]
            }
        }
        return ""
    }
    
    private func getAccommodationKeyfromValue(passedValue: String) -> String {
        for i in 0..<self.accoDetailValues.count {
            if self.accoDetailValues[i] == passedValue {
                return self.accoIDValues[i]
            }
        }
        return ""
    }
    
    //MARK: Picker Control Delegates
    func numberOfComponents(in pickerView: UIPickerView) -> Int {
        return 1
    }
    
    func pickerView(_ pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int {
        if pickerView == accoPicker {
            return accoDetailValues.count
        }
        else {
            return 1
        }
    }
    
    func pickerView(_ pickerView: UIPickerView, titleForRow row: Int, forComponent component: Int) -> String? {
        if pickerView == accoPicker {
            return accoDetailValues[row]
        }
        else {
            return ""
        }
    }
    
    func pickerView(_ pickerView: UIPickerView, didSelectRow row: Int, inComponent component: Int){
        if pickerView == accoPicker {
            txtAccommodation.text = accoDetailValues[row]
            self.view.endEditing(true)
        }
    }
    
    //MARK: Text Field Delegates
    func textFieldShouldReturn(_ textField: UITextField) -> Bool {
        textField.resignFirstResponder()
        return true
    }
    
    func textFieldDidEndEditing(_ textField: UITextField) {
        // lblTopMessage.text = "Editing..."
    }
    
    func textViewDidEndEditing(_ textView: UITextView) {
        //updateSaveButtonState()
        //navigationItem.title = textField.text
    }
    
    func textFieldDidBeginEditing(_ textField: UITextField) {
        // Disable the Save button while editing.
        /*saveButton.isEnabled = false
         btnSavePrint.isEnabled = false
         btnSave.isEnabled=false
         btnSaveExit.isEnabled=false */
        //        pickerDevoteeType.isHidden = false
    }
    
    
    
    // MARK: - Navigation
/*
     override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
     // Get the new view controller using segue.destinationViewController.
     // Pass the selected object to the new view controller.
     super.prepare(for: segue, sender: sender)
     
     switch(segue.identifier ?? "") {
     case "AddDevotee":
     os_log("Adding a new devotee.", log: OSLog.default, type: .debug)
     
     case "ShowDetail":
    
     //print(devoteeDetailViewController.devotee?.devoteeAccoId)
     default:
     fatalError("Unexpected Segue Identifier; \(String(describing: segue.identifier))")
     }
     }*/
    
    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        // Get the new view controller using segue.destination.
        // Pass the selected object to the new view controller.
       // searchDevotee(self)
        //print("reaching prepare...")
        var searchStr: String = ""
        if txtDevoteeKey.text != ""   {
            searchDevoteeDetail(completion: {
                //self.txtAccommodation.inputView = self.accoPicker
//                let storyboard = UIStoryboard(name: "Main", bundle: nil)
                guard let devoteeViewController = segue.destination as? DevoteeViewController else {
                    fatalError("Unexpected destination: \(segue.destination)")
                }
//                let devoteeViewController = storyboard.instantiateViewController(withIdentifier:"DevoteeViewController") as? DevoteeViewController
                devoteeViewController.devotee  = self.devotee
                self.navigationController?.pushViewController(devoteeViewController, animated: true)
            })
        } else {
            searchStr = "mode=CUS&key"
            
            if txtRemarks.text != "" {
                searchStr = searchStr + "remarks=" + txtRemarks.text.addingPercentEncoding(withAllowedCharacters: .alphanumerics)! + ","
            }
            if txtStation.text != "" {
                searchStr = searchStr + "Station=" + txtStation.text! + ","
            }
            if txtIDNumber.text != "" {
                searchStr = searchStr + "id_number=" + txtIDNumber.text! + ","
            }
            if txtLastName.text != "" {
                searchStr = searchStr + "last_name=" + txtLastName.text! + ","
            }
            if txtFirstName.text != "" {
                searchStr = searchStr + "first_name=" + txtFirstName.text! + ","
            }
            if txtAccommodation.text != "" {
                searchStr = searchStr + "accomodation=" + getAccommodationKeyfromValue(passedValue: txtAccommodation.text!) + ","
            }
            if txtPhoneNumber.text != "" {
                searchStr = searchStr + "devotee_cell_phone_number=" + txtPhoneNumber.text! + ","
            }
            if searchStr.count > 0 {
                searchStr =  String(searchStr.dropLast())
            }
        }
    }
 

}
