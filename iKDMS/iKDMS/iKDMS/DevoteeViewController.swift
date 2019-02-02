//
//  DevoteeViewController.swift
//  iKDMS
//
//  Created by Gupta, Anil on 1/23/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import UIKit
import os.log

class DevoteeViewController: UIViewController, UITextFieldDelegate, UITextViewDelegate, UIImagePickerControllerDelegate, UINavigationControllerDelegate {
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
    var devotee: Devotee?
    
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
        
        loadMasterData()
        
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
            txtAccommodation.text = devotee.devoteeAccoId
            txtRemarks.text = devotee.devoteeRemarks
            DevoteePhoto.image = devotee.devoteePhoto
            devoteeIDImage.image = devotee.devoteeIdImage
        }
        
        
        
        updateSaveButtonState()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
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
        
        devotee = Devotee(firstName: txtFirstName.text ?? "", lastName: txtLastName.text ?? "", devoteeKey: txtDevoteeKey.text ?? "D1", devoteeType: txtDevoteeType.text ?? "P", devoteeIdType: txtIDType.text ?? "", devoteeIdNumber: txtIDNumber.text ?? "",  devoteeStation: txtStation.text ?? "", devoteePhone: txtPhoneNumber.text ?? "", devoteeRemarks: txtRemarks.text ?? "", devoteeAccoId: txtAccommodation.text ?? "", devoteePhoto: DevoteePhoto.image, devoteeIdImage:devoteeIDImage.image)
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
        let text = txtDevoteeKey.text ?? ""
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
            imagePickerController.sourceType = .photoLibrary
            
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
        
        // Dismiss the picker.
        dismiss(animated: true, completion: nil)
    }
    
    @IBAction func btnSave(_ sender: UIButton) {
        //lblTopMessage.text = "Devotee Record Saved!!"
    }
    @IBAction func btnSavePrint(_ sender: UIButton) {
        //lblTopMessage.text = txtFirstName.text! + ", " + txtLastName.text!
    }
    @IBAction func btnSaveExit(_ sender: UIButton) {
        //lblTopMessage.text = "Devotee Record Saved, exiting now.."
    }
    
    private func loadMasterData() {
        let urlString = "http://localhost/KDMS/api/loadoptions.php?option_type=Accommodation"
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
                    print(parsedData[1])
                    //self.Accmmodation = articlesData
                    //self.collectionView?.reloadData()
                }
                
            } catch let jsonError {
                print(jsonError)
            }
            }.resume()
    }
}


