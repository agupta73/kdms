//
//  DevoteeViewController.swift
//  iKDMS
//
//  Created by Gupta, Anil on 1/23/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import UIKit
import os.log
import Foundation

class DevoteeViewController: UIViewController, UITextFieldDelegate, UITextViewDelegate, UIImagePickerControllerDelegate, UINavigationControllerDelegate, UIPickerViewDelegate, UIPickerViewDataSource {
    //MARK: Properties
    //@IBOutlet weak var lblTopMessage: UILabel!
    @IBOutlet weak var txtDevoteeKey: UITextField!
    @IBOutlet weak var txtFirstName: UITextField!
    @IBOutlet weak var txtLastName: UITextField!
    @IBOutlet weak var txtDevoteeType: UITextField!
    @IBOutlet weak var txtIDType: UITextField!
    @IBOutlet weak var txtIDNumber: UITextField!
    @IBOutlet weak var txtPhoneNumber: UITextField!
    @IBOutlet weak var txtStation: UITextField!
    @IBOutlet weak var txtAccommodation: UITextField!
    @IBOutlet weak var txtRemarks: UITextView!
    @IBOutlet weak var DevoteePhoto: UIImageView!
    @IBOutlet weak var devoteeIDImage: UIImageView!
    @IBOutlet weak var saveButton: UIBarButtonItem!
    @IBOutlet weak var btnSavePrint: UIButton!
    @IBOutlet weak var btnSave: UIButton!
    @IBOutlet weak var btnSaveExit: UIButton!
    
    
  
    struct AccommodationStructure: Codable {
        var accomodation_key: String
        var Accomodation_Name: String
        var Available_Count: String
        var Accomodation_Capacity: String
        var Allocated_Count: String
        var Reserved_Count: String
        var Out_Of_Availability_Count: String
    }
    
    struct DevoteeStructure: Codable {
        var Devotee_Key: String
        var Devotee_Type: String?
        var Devotee_First_Name: String?
        var Devotee_Last_Name: String?
        var Devotee_Gender: String?
        var Devotee_ID_Type: String?
        var Devotee_ID_Number: String?
        var Devotee_Station: String?
        var Devotee_Cell_Phone_Number: String?
        var Devotee_Status: String?
        var Devotee_Remarks: String?
        var Devotee_Record_Update_Date_Time: String?
        var Devotee_Record_Updated_By: String?
        var Devotee_ID_Image: String?
        var Devotee_ID_XML: String?
        var DID_Devotee_ID_Type: String?
        var Photo_type: String?
        var Devotee_Photo: String?
        var Accomodation_Key: String
        
    }
    
    var devotee: Devotee?
    var accoDetailValues: [String] = Array()
    var accoIDValues: [String] = Array()
    var devoteeIDTypeValues: [String] = Array()
    var devoteeTypeValues: [String] = Array()
    
    let accoPicker = UIPickerView()
    let idTypePicker = UIPickerView()
    let devoteeTypePicker = UIPickerView()
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        //Delegate for text fields
        txtFirstName.delegate = self
        txtDevoteeKey.delegate = self
        txtLastName.delegate = self
        txtDevoteeType.delegate = self
        txtIDType.delegate = self
        txtIDNumber.delegate = self
        txtPhoneNumber.delegate = self
        txtStation.delegate = self
        txtAccommodation.delegate = self
        txtRemarks.delegate = self
        accoPicker.delegate = self
        accoPicker.dataSource = self
        idTypePicker.delegate = self
        idTypePicker.dataSource = self
        devoteeTypePicker.delegate = self
        devoteeTypePicker.dataSource = self
        
        loadMasterData()
        
        txtAccommodation.inputView = self.accoPicker
        txtIDType.inputView = self.idTypePicker
        txtDevoteeType.inputView = self.devoteeTypePicker
        
        // Set up views if editing an existing Devotee.
        if let devotee = devotee {
            navigationItem.title = devotee.firstName! + " " + devotee.lastName!
            txtFirstName.text = devotee.firstName
            txtDevoteeKey.text = devotee.devoteeKey
            txtLastName.text = devotee.lastName
            txtDevoteeType.text = devotee.devoteeType
            txtIDType.text = devotee.devoteeIdType
            txtIDNumber.text = devotee.devoteeIdNumber
            txtPhoneNumber.text = devotee.devoteePhone
            txtStation.text = devotee.devoteeStation
            txtAccommodation.text = getAccommodationValuefromKey(passedKey: devotee.devoteeAccoId!)
            txtRemarks.text = devotee.devoteeRemarks
            DevoteePhoto.image = devotee.devoteePhoto
            devoteeIDImage.image = devotee.devoteeIdImage
        }
        
        
        //updateSaveButtonState()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }

    //MARK: Picker Control Delegates
    func numberOfComponents(in pickerView: UIPickerView) -> Int {
        return 1
    }
    
    func pickerView(_ pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int {
        if pickerView == accoPicker {
            return accoDetailValues.count
        }
        else if pickerView == idTypePicker {
            return devoteeIDTypeValues.count
        }
        else if pickerView == devoteeTypePicker {
            return devoteeTypeValues.count
        }
        else {
            return 1
        }
    }
    
    func pickerView(_ pickerView: UIPickerView, titleForRow row: Int, forComponent component: Int) -> String? {
        if pickerView == accoPicker {
            return accoDetailValues[row]
        }
        else if pickerView == idTypePicker {
            return devoteeIDTypeValues[row]
        }
        else if pickerView == devoteeTypePicker {
            return devoteeTypeValues[row]
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
        else if pickerView == idTypePicker {
        txtIDType.text = devoteeIDTypeValues[row]
        self.view.endEditing(true)
    }
        else if pickerView == devoteeTypePicker {
            txtDevoteeType.text = devoteeTypeValues[row]
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
        updateSaveButtonState()
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
    
    //MARK: Navigation
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        super.prepare(for: segue, sender: sender)
        // Configure the destination view controller only when the save button is pressed.
        //guard let button = sender as? UIBarButtonItem, button === saveButton || button === btnSavePrint else {
            guard let button = sender as? UIButton, button === btnSavePrint else {
            os_log("The save buttons was not pressed, cancelling", log: OSLog.default, type: .debug)
            return
        }
        
        devotee = Devotee(firstName: txtFirstName.text ?? "", lastName: txtLastName.text ?? "", devoteeKey: txtDevoteeKey.text ?? "", devoteeType: txtDevoteeType.text ?? "P", devoteeIdType: txtIDType.text ?? "", devoteeIdNumber: txtIDNumber.text ?? "",  devoteeStation: txtStation.text ?? "", devoteePhone: txtPhoneNumber.text ?? "", devoteeRemarks: txtRemarks.text ?? "", devoteeAccoId: txtAccommodation.text ?? "", devoteePhoto: DevoteePhoto.image, devoteeIdImage:devoteeIDImage.image)
        os_log("Devotee Object created", log: OSLog.default, type: .debug)
    }
    
    @IBAction func Cancel(_ sender: Any) {
        // Depending on style of presentation (modal or push presentation), this view controller needs to be dismissed in two different ways.
        let isPresentingInAddMealMode = presentingViewController is UINavigationController
        
        if isPresentingInAddMealMode {
            dismiss(animated: true, completion: nil)
        }
        else if let owningNavigationController = navigationController{
            owningNavigationController.popViewController(animated: true)
        }
        else {
            fatalError("The DevoteeViewController is not inside a navigation controller.")
        }
    }
    
    //MARK: Private Method
    private func updateSaveButtonState() {
        // Disable the Save button if the text field is empty.
        let text = txtFirstName.text ?? ""
        saveButton.isEnabled = !text.isEmpty
        btnSaveExit.isEnabled = !text.isEmpty
        btnSave.isEnabled = !text.isEmpty
        btnSavePrint.isEnabled = !text.isEmpty
    }
    
    //MARK: Action
    
    @IBAction func PhotoClicked(_ sender: UITapGestureRecognizer) {
        
            txtFirstName.resignFirstResponder()
            txtDevoteeKey.resignFirstResponder()
            txtLastName.resignFirstResponder()
            txtDevoteeType.resignFirstResponder()
            txtIDType.resignFirstResponder()
            txtIDNumber.resignFirstResponder()
            txtPhoneNumber.resignFirstResponder()
            txtStation.resignFirstResponder()
            txtAccommodation.resignFirstResponder()
            txtRemarks.resignFirstResponder()
            let imagePickerController = UIImagePickerController()
            imagePickerController.sourceType = .camera
            
            // Make sure ViewController is notified when the user picks an image.
            imagePickerController.delegate = self
            present(imagePickerController, animated: true, completion: nil)
            //lblTopMessage.text = "photo clicked.."
    }
    //MARK: UIImagePickerControllerDelegate
    func imagePickerControllerDidCancel(_ picker: UIImagePickerController) {
        // Dismiss the picker if the user canceled.
        dismiss(animated: true, completion: nil)
    }
    
    func imagePickerController(_ picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [String : Any]) {
        
        // The info dictionary may contain multiple representations of the image. You want to use the original.
        guard let selectedImage = info[UIImagePickerControllerOriginalImage] as? UIImage else {
            fatalError("Expected a dictionary containing an image, but was provided the following: \(info)")
        }
        
        // Set photoImageView to display the selected image.
        DevoteePhoto.image = selectedImage
        saveDevoteePhoto(selectedImage: selectedImage)
        // Dismiss the picker.
        dismiss(animated: true, completion: nil)
    }
    
    @IBAction func btnSave(_ sender: UIButton) {
        saveDevotee(toPrintCard: false)
    }
    @IBAction func btnSavePrint(_ sender: UIButton) {
        saveDevotee(toPrintCard: true)
    }
    @IBAction func btnSaveExit(_ sender: UIButton) {
        //lblTopMessage.text = "Devotee Record Saved, exiting now.."
    }
    
    private func loadMasterData() {
        loadDevoteeRecord()
        loadAccommodations()
        
        //Load deveotee ID Types
        devoteeIDTypeValues.append("none")
        devoteeIDTypeValues.append("Adhaar")
        devoteeIDTypeValues.append("DL")
        devoteeIDTypeValues.append("Other")
        devoteeIDTypeValues.append("PAN")
        devoteeIDTypeValues.append("Passport")
        devoteeIDTypeValues.append("Voter ID")
        
        //Load deveotee Types
        devoteeTypeValues.append("P")
        devoteeTypeValues.append("T")
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
    private func loadAccommodations() {
        let urlString = "http://FSCAM0RLHV2R.local/KDMS/api/loadoptions.php?option_type=Accommodation"
        guard let url = URL(string: urlString) else { return }
        
        URLSession.shared.dataTask(with: url) { (data, response, error) in
            if error != nil {
                print(error!.localizedDescription)
            }
            
            guard let data = data else { return }
            //Implement JSON decoding and parsing
            do {
                //Decode retrived data with JSONDecoder and assing type of Article object
                let parsedData = try JSONDecoder().decode([AccommodationStructure].self, from: data)
                
                //Get back to the main queue
                DispatchQueue.main.async {
                    
                    for i in 0..<parsedData.count {
                        self.accoDetailValues.append(parsedData[i].Accomodation_Name + " - " + parsedData[i].Available_Count)
                        self.accoIDValues.append(parsedData[i].accomodation_key)
                    }
                }
                
            } catch let jsonError {
                print(jsonError)
            }
            }.resume()
    }
    private func loadDevoteeRecord() {
        if(txtDevoteeKey.text != "") {
            let urlString = "http://FSCAM0RLHV2R.local/KDMS/api/searchDevotee.php?mode=KEY&key=" + txtDevoteeKey.text!
            //print(urlString)
            guard let url = URL(string: urlString) else { return }
            
            URLSession.shared.dataTask(with: url) { (data, response, error) in
                if error != nil {
                    print(error!.localizedDescription)
                }
                
                guard let data = data else { return }
                //Implement JSON decoding and parsing
                do {
                    //Decode retrived data with JSONDecoder and assing type of Article object
                    let parsedData = try JSONDecoder().decode(DevoteeStructure.self, from: data)
                    
                    //Get back to the main queue
                    DispatchQueue.main.async {
                        self.txtDevoteeKey.text = parsedData.Devotee_Key
                        self.txtRemarks.text = parsedData.Devotee_Remarks
                        self.txtFirstName.text = parsedData.Devotee_First_Name
                        self.txtLastName.text = parsedData.Devotee_Last_Name
                        self.txtDevoteeType.text = parsedData.Devotee_Type
                        self.txtIDType.text = parsedData.Devotee_ID_Type
                        self.txtIDNumber.text = parsedData.Devotee_ID_Number
                        self.txtPhoneNumber.text = parsedData.Devotee_Cell_Phone_Number
                        self.txtStation.text = parsedData.Devotee_Station
                        self.txtAccommodation.text = parsedData.Accomodation_Key
                    }
                    
                } catch let jsonError {
                    print(jsonError)
                }
                }.resume()
            
            }
        }

    
    
    func saveDevotee(toPrintCard: Bool) {
        let headers = ["Content-Type": "application/x-www-form-urlencoded"]
        let postData = NSMutableData(data: "devotee_type=".data(using: String.Encoding.utf8)!)
        
        postData.append((txtDevoteeType.text?.data(using: String.Encoding.utf8)!)!)
        postData.append("&devotee_first_name=".data(using: String.Encoding.utf8)!)
        postData.append((txtFirstName.text?.data(using: String.Encoding.utf8)!)!)
        postData.append("&devotee_last_name=".data(using: String.Encoding.utf8)!)
        postData.append((txtLastName.text?.data(using: String.Encoding.utf8)!)!)
        postData.append("&devotee_gender=".data(using: String.Encoding.utf8)!)
        postData.append("".data(using: String.Encoding.utf8)!)
        postData.append("&devotee_id_type=".data(using: String.Encoding.utf8)!)
        postData.append((txtIDType.text?.data(using: String.Encoding.utf8)!)!)
        postData.append("&devotee_id_number=".data(using: String.Encoding.utf8)!)
        postData.append((txtIDNumber.text?.data(using: String.Encoding.utf8)!)!)
        postData.append("&devotee_station=".data(using: String.Encoding.utf8)!)
        postData.append((txtStation.text?.data(using: String.Encoding.utf8)!)!)
        postData.append("&devotee_cell_phone_number=".data(using: String.Encoding.utf8)!)
        postData.append((txtPhoneNumber.text?.data(using: String.Encoding.utf8)!)!)
        postData.append("&devotee_status=".data(using: String.Encoding.utf8)!)
        postData.append("A".data(using: String.Encoding.utf8)!)
        postData.append("&devotee_remarks=".data(using: String.Encoding.utf8)!)
        postData.append((txtRemarks.text?.data(using: String.Encoding.utf8)!)!)
        postData.append("&devotee_accommodation_id=".data(using: String.Encoding.utf8)!)
        postData.append((getAccommodationKeyfromValue(passedValue: txtAccommodation.text!).data(using: String.Encoding.utf8)!))
        postData.append("&devotee_key=".data(using: String.Encoding.utf8)!)
        postData.append((txtDevoteeKey.text?.data(using: String.Encoding.utf8)!)!)
        postData.append("&requestType=upsertDevotee".data(using: String.Encoding.utf8)!)
        
        //print(postData)
        
        let todosEndpoint: String = "http://FSCAM0RLHV2R.local/KDMS/api/upsertDevotee.php"
        guard let todosURL = URL(string: todosEndpoint) else {
            print("Error: cannot create URL")
            return
        }
        
        var todosUrlRequest = URLRequest(url: todosURL)
        todosUrlRequest.httpMethod = "POST"
        todosUrlRequest.addValue("application/x-www-form-urlencoded; charset=utf-8", forHTTPHeaderField: "Content-Type")
        todosUrlRequest.addValue("application/json", forHTTPHeaderField: "Accept")
        todosUrlRequest.httpMethod = "POST"
        todosUrlRequest.allHTTPHeaderFields = headers
        todosUrlRequest.httpBody = postData as Data
        
        let session = URLSession.shared
        
        let task = session.dataTask(with: todosUrlRequest) {
            (data, response, error) in
            guard error == nil else {
                print("error calling POST on /todos/1")
                print(error!)
                return
            }
            guard let responseData = data else {
                print("Error: did not receive data")
                return
            }
            
            // parse the result as JSON, since that's what the API provides
            do {
                
                guard let receivedTodo = try JSONSerialization.jsonObject(with: responseData, options: .mutableContainers) as? [String: Any] else {
                    print("Could not get JSON from responseData as dictionary")
                    return
                }
                print("The todo is: " + receivedTodo.description)
                guard let saved = receivedTodo["flag"] as? Bool else {
                    print("Could not get todoID as string from JSON")
                    return
                }
                if saved {
                    print("The ID is: \(receivedTodo["info"] ?? "")")
                    self.navigationItem.title = "Record Saved: "
                    if toPrintCard {
                        self.printDevoteeCard(devoteeKey: receivedTodo["info"] as! String)
                    }
                }
            } catch  {
                print("error parsing response from POST on /todos")
                self.navigationItem.title = "Error!!"
                return
            }
        }
        task.resume()
    }
    
    private func printDevoteeCard(devoteeKey: String){
        let headers = ["Content-Type": "application/x-www-form-urlencoded"]
        let postData = NSMutableData(data: "&devotee_key=".data(using: String.Encoding.utf8)!)
        postData.append((devoteeKey.data(using: String.Encoding.utf8)!))
        postData.append("&requestType=addToPrintQueue".data(using: String.Encoding.utf8)!)
       
        let todosEndpoint: String = "http://FSCAM0RLHV2R.local/KDMS/api/upsertDevotee.php"
        guard let todosURL = URL(string: todosEndpoint) else {
            print("Error: cannot create URL")
            return
        }
        
        var todosUrlRequest = URLRequest(url: todosURL)
        todosUrlRequest.httpMethod = "POST"
        todosUrlRequest.addValue("application/x-www-form-urlencoded; charset=utf-8", forHTTPHeaderField: "Content-Type")
        todosUrlRequest.addValue("application/json", forHTTPHeaderField: "Accept")
        todosUrlRequest.httpMethod = "POST"
        todosUrlRequest.allHTTPHeaderFields = headers
        todosUrlRequest.httpBody = postData as Data
        
        let session = URLSession.shared
        
        let task = session.dataTask(with: todosUrlRequest) {
            (data, response, error) in
            guard error == nil else {
                print("error calling POST on /todos/1")
                print(error!)
                return
            }
            guard let responseData = data else {
                print("Error: did not receive data")
                return
            }
            
            // parse the result as JSON, since that's what the API provides
            do {
                
                guard let receivedTodo = try JSONSerialization.jsonObject(with: responseData, options: .mutableContainers) as? [String: Any] else {
                    print("Could not get JSON from responseData as dictionary")
                    return
                }
                print("The todo is: " + receivedTodo.description)
                guard let saved = receivedTodo["flag"] as? Bool else {
                    print("Could not get todoID as string from JSON")
                    return
                }
                if saved {
                    print("The ID is: \(receivedTodo["info"] ?? "")")
                    self.navigationItem.title = "Record Saved and Printed!"
                }
            } catch  {
                print("error parsing response from POST on /todos")
                self.navigationItem.title = "Error!!"
                return
            }
        }
        task.resume()
    }
    
    private func saveDevoteePhoto(selectedImage: UIImage, imageType: String = "") {
        let imageData = UIImageJPEGRepresentation(selectedImage,0.2)
        //let imageData = UIImagePNGRepresentation(selectedImage)
        let base64String = imageData?.base64EncodedData()
        
        
        //print(base64String?.description)
        
        let headers = ["Content-Type": "application/x-www-form-urlencoded"]
        let postData = NSMutableData(data: "&devotee_key=".data(using: String.Encoding.utf8)!)
        postData.append((self.devotee?.devoteeKey?.data(using: String.Encoding.utf8) ?? "".data(using: String.Encoding.utf8)!))
        postData.append("&api_type=3".data(using: String.Encoding.utf8)!)
        postData.append("&image=".data(using: String.Encoding.utf8)!)
        postData.append(base64String!)
        
        let todosEndpoint: String = "http://FSCAM0RLHV2R.local/KDMS/api/managePhotoIOS.php"
        guard let todosURL = URL(string: todosEndpoint) else {
            print("Error: cannot create URL")
            return
        }
        
        var todosUrlRequest = URLRequest(url: todosURL)
        todosUrlRequest.httpMethod = "POST"
        todosUrlRequest.addValue("application/x-www-form-urlencoded; charset=utf-8", forHTTPHeaderField: "Content-Type")
        todosUrlRequest.addValue("application/json", forHTTPHeaderField: "Accept")
        todosUrlRequest.httpMethod = "POST"
        todosUrlRequest.allHTTPHeaderFields = headers
        todosUrlRequest.httpBody = postData as Data
        
        let session = URLSession.shared
        
        let task = session.dataTask(with: todosUrlRequest) {
            (data, response, error) in
            guard error == nil else {
                print("error calling POST on /todos/1")
                print(error!)
                return
            }
            guard let responseData = data else {
                print("Error: did not receive data")
                return
            }
            print(responseData.description)
            // parse the result as JSON, since that's what the API provides
            do {
                
                guard let receivedTodo = try JSONSerialization.jsonObject(with: responseData, options: .mutableContainers) as? [String: Any] else {
                    print("Could not get JSON from responseData as dictionary")
                    return
                }
                print("The todo is: " + receivedTodo.description)
                guard let saved = receivedTodo["flag"] as? Bool else {
                    print("Could not get todoID as string from JSON")
                    return
                }
                if saved {
                    print("The ID is: \(receivedTodo["info"] ?? "")")
                    self.navigationItem.title = "Record Saved and Printed!"
                }
            } catch  {
                print("error parsing response from POST on /todos")
                self.navigationItem.title = "Error!!"
                return
            }
        }
        task.resume()
        
        //print(base64String as Any)
    }
}


