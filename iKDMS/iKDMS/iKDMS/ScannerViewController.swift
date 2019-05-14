//
//  ScannerViewController.swift
//  iKDMS
//
//  Created by Gupta, Anil on 2/13/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import AVFoundation
import UIKit
import Alamofire

class ScannerViewController: UIViewController, AVCaptureMetadataOutputObjectsDelegate, XMLParserDelegate {
    var captureSession: AVCaptureSession!
    var previewLayer: AVCaptureVideoPreviewLayer!
    
//    var xmlString: String
    
    @IBAction func Cancel(_ sender: Any) {
        
        self.navigationController?.popViewController(animated: true)
//        let isPresentingInAddMealMode = presentingViewController is UINavigationController
//        
//        if isPresentingInAddMealMode {
//            dismiss(animated: true, completion: nil)
//        }
//        else if let owningNavigationController = navigationController{
//            owningNavigationController.popViewController(animated: true)
//        }
//        else {
//            fatalError("The ScannerControl is not inside a navigation controller.")
//        }
    }
    
    
  
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        
       
        
         /* Alamofire.request("https://httpbin.org/get").responseJSON { response in
            print("Request: \(String(describing: response.request))")   // original url request
            print("Response: \(String(describing: response.response))") // http url response
            print("Result: \(response.result)")                         // response serialization result
            
            if let json = response.result.value {
                print("JSON: \(json)") // serialized json response
            }
            
            if let data = response.data, let utf8Text = String(data: data, encoding: .utf8) {
                print("Data: \(utf8Text)") // original server data as UTF8 string
            }
        }
 */
        //view.backgroundColor = UIColor.black
        captureSession = AVCaptureSession()
        
        guard let videoCaptureDevice = AVCaptureDevice.default(for: .video) else { return }
        let videoInput: AVCaptureDeviceInput
        
        do {
            videoInput = try AVCaptureDeviceInput(device: videoCaptureDevice)
        } catch {
            return
        }
        
        if (captureSession.canAddInput(videoInput)) {
            captureSession.addInput(videoInput)
        } else {
            failed()
            return
        }
        
        let metadataOutput = AVCaptureMetadataOutput()
        
        if (captureSession.canAddOutput(metadataOutput)) {
            captureSession.addOutput(metadataOutput)
            
            metadataOutput.setMetadataObjectsDelegate(self, queue: DispatchQueue.main)
            metadataOutput.metadataObjectTypes = [.qr]
        } else {
            failed()
            return
        }
        
        previewLayer = AVCaptureVideoPreviewLayer(session: captureSession)
        previewLayer.frame = view.layer.bounds
        previewLayer.videoGravity = .resizeAspectFill
        view.layer.addSublayer(previewLayer)
        
        captureSession.startRunning()
    }
    
    func failed() {
        let ac = UIAlertController(title: "Scanning not supported", message: "Your device does not support scanning a code from an item. Please use a device with a camera.", preferredStyle: .alert)
        ac.addAction(UIAlertAction(title: "OK", style: .default))
        present(ac, animated: true)
        captureSession = nil
    }
    
    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        
        if (captureSession?.isRunning == false) {
            captureSession.startRunning()
        }
    }
    
    override func viewWillDisappear(_ animated: Bool) {
        super.viewWillDisappear(animated)
        
        if (captureSession?.isRunning == true) {
            captureSession.stopRunning()
        }
    }
    
    func metadataOutput(_ output: AVCaptureMetadataOutput, didOutput metadataObjects: [AVMetadataObject], from connection: AVCaptureConnection) {
        captureSession.stopRunning()
        
        if let metadataObject = metadataObjects.first {
            guard let readableObject = metadataObject as? AVMetadataMachineReadableCodeObject else { return }
            guard let stringValue = readableObject.stringValue else { return }
            AudioServicesPlaySystemSound(SystemSoundID(kSystemSoundID_Vibrate))
            found(code: stringValue)
        }
        
        dismiss(animated: true)
    }
    
    func found(code: String) {
        print(code)
        
        let xmlData = code.data(using: String.Encoding.utf8)!
        
        let parser = XMLParser(data: xmlData)
            parser.delegate = self
            parser.parse()
        
        //XML from the QR code of Aadhar Card:
        /* <?xml version="1.0" encoding="UTF-8"?>
        <PrintLetterBarcodeData uid="501254195869" name="KUNDAN SINGH GAIRA" gender="M" yob="1988" co="S/O THAKUR SINGH GAIRA" house="." street="UPPER MALL WARD-8" loc="BASANT VIHAR LOWER DANDA" vtc="Nainital" dist="Nainital" state="Uttarakhand" pc="263002"/> */
    }
    
    override var prefersStatusBarHidden: Bool {
        return true
    }
    
    override var supportedInterfaceOrientations: UIInterfaceOrientationMask {
        return .portrait
    }
    
    func parser(_ parser: XMLParser, didStartElement elementName: String, namespaceURI: String?, qualifiedName qName: String?, attributes attributeDict: [String : String] = [:]) {
        let photo1 = UIImage(named:"Devotee1")
        let image1 = UIImage(named:"ID1")
        var name: String = ""
        
        var firstName: String = ""
        var lastName: String = ""
        var location: String = ""
        var ID: String = ""
        var IDType: String = ""
        
        name = attributeDict["name"] ?? ""
        let fullName = name.split(separator: " ")
        lastName = String(fullName[fullName.count - 1])
        firstName = name.replacingOccurrences(of: lastName, with: "")
        
       // let gender = attributeDict["gender"] ?? ""
        ID =  attributeDict["uid"] ?? ""
        IDType =  "Adhaar"
        location = attributeDict["dist"] ?? ""
        var remark: String = "Address: "
        remark += attributeDict["house"] ?? ""
        remark += ", "
        remark +=  attributeDict["street"] ?? ""
        remark += ", "
        remark +=  attributeDict["loc"] ?? ""
        remark += ", "
        remark +=  attributeDict["vtc"] ?? ""
        remark += ", "
        remark +=   attributeDict["state"] ?? ""
        remark += ", "
        remark +=  attributeDict["pc"] ?? ""
        
        let newDevotee = Devotee(firstName: firstName , lastName: lastName , devoteeKey: "", devoteeType: "P", devoteeIdType: IDType, devoteeIdNumber: ID , devoteeStation: location, devoteePhone: "", devoteeRemarks: remark , devoteeAccoId: "", devoteeAccoName: "", devoteePhoto: photo1, devoteeIdImage: image1)
    
        
        let storyboard = UIStoryboard(name: "Main", bundle: nil)
        let devoteeViewController = storyboard.instantiateViewController(withIdentifier:"DevoteeViewController") as? DevoteeViewController
        
        devoteeViewController?.devotee  = newDevotee
        
        self.navigationController?.pushViewController(devoteeViewController!, animated: true)
    }
    // 2
    

}
