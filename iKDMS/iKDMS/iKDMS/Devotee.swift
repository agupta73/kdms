//
//  Devotee.swift
//  iKDMS
//
//  Created by Gupta, Anil on 1/24/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import UIKit
import os.log

class Devotee {
    //MARK: Properties
    var firstName: String?
    var lastName: String?
    var devoteeKey: String?
    var devoteeType: String?
    var devoteeIdType: String?
    var devoteeIdNumber: String?
    var devoteeStation: String?
    var devoteePhone: String?
    var devoteeRemarks: String?
    var devoteeAccoId: String?
    var devoteeAccoName: String?
    var devoteePhoto: UIImage?
    var devoteeIdImage: UIImage?
    
    required convenience init?(coder aDecoder: NSCoder) {
        //Name is required. If we cant decode a name string, the initializer should fail
        guard let devoteeKey = aDecoder.decodeObject(forKey: PropertyKey.devoteeKey) as? String
            else {
                os_log("Unable to decode the devotee key for a Devotee Record Object", log: OSLog.default, type: .debug)
                return nil
        }
        
        let firstName = aDecoder.decodeObject(forKey: PropertyKey.firstName) as? String
        let lastName = aDecoder.decodeObject(forKey: PropertyKey.lastName) as? String
        //let devoteeKey = aDecoder.decodeObject(forKey: PropertyKey.devoteeKey) as String
        let devoteeType = aDecoder.decodeObject(forKey: PropertyKey.devoteeType) as? String
        let devoteeIdType = aDecoder.decodeObject(forKey: PropertyKey.devoteeIdType) as? String
        let devoteeIdNumber = aDecoder.decodeObject(forKey: PropertyKey.devoteeIdNumber) as? String
        let devoteeStation = aDecoder.decodeObject(forKey: PropertyKey.devoteeStation) as? String
        let devoteePhone = aDecoder.decodeObject(forKey: PropertyKey.devoteePhone) as? String
        let devoteeRemarks = aDecoder.decodeObject(forKey: PropertyKey.devoteeRemarks) as? String
        let devoteeAccoId = aDecoder.decodeObject(forKey: PropertyKey.devoteeAccoId) as? String
        let devoteeAccoName = aDecoder.decodeObject(forKey: PropertyKey.devoteeAccoName) as? String
        let devoteePhoto = aDecoder.decodeObject(forKey: PropertyKey.devoteePhoto) as? UIImage
        let devoteeIdImage = aDecoder.decodeObject(forKey: PropertyKey.devoteeIdImage) as? UIImage
        
        // self.init(name: name, photo: photo, rating: rating)
        self.init(firstName: firstName, lastName: lastName, devoteeKey: devoteeKey, devoteeType: devoteeType, devoteeIdType: devoteeIdType, devoteeIdNumber: devoteeIdNumber, devoteeStation: devoteeStation, devoteePhone: devoteePhone, devoteeRemarks: devoteeRemarks, devoteeAccoId: devoteeAccoId,devoteeAccoName: devoteeAccoName, devoteePhoto: devoteePhoto, devoteeIdImage: devoteeIdImage )
    }
    
    func encode(with aCoder: NSCoder) {
        aCoder.encode(firstName, forKey:PropertyKey.firstName)
        aCoder.encode(lastName, forKey:PropertyKey.lastName)
        aCoder.encode(devoteeKey, forKey:PropertyKey.devoteeKey)
        aCoder.encode(devoteeType, forKey:PropertyKey.devoteeType)
        aCoder.encode(devoteeIdType, forKey:PropertyKey.devoteeIdType)
        aCoder.encode(devoteeIdNumber, forKey:PropertyKey.devoteeIdNumber)
        aCoder.encode(devoteeStation, forKey:PropertyKey.devoteeStation)
        aCoder.encode(devoteePhone, forKey:PropertyKey.devoteePhone)
        aCoder.encode(devoteeRemarks, forKey:PropertyKey.devoteeRemarks)
        aCoder.encode(devoteeAccoId, forKey:PropertyKey.devoteeAccoId)
        aCoder.encode(devoteeAccoName, forKey:PropertyKey.devoteeAccoName)
        aCoder.encode(devoteePhoto, forKey:PropertyKey.devoteePhoto)
        aCoder.encode(devoteeIdImage, forKey:PropertyKey.devoteeIdImage)
        
        //aCoder.encode(rating, forKey: PropertyKey.rating)
    }
    
    //MARK: Archiving Path
    static let DocumentsDirectory =  FileManager().urls(for: .documentDirectory, in: .userDomainMask).first!
    static let ArchiveURL = DocumentsDirectory.appendingPathComponent("devotee")
    
    //MARK: Type
    struct PropertyKey {
        static let firstName = "firstName"
        static let lastName = "lastName"
        static let devoteeKey = "devoteeKey"
        static let devoteeType = "devoteeType"
        static let devoteeIdType = "devoteeIdType"
        static let devoteeIdNumber = "devoteeIdNumber"
        static let devoteeStation = "devoteeStation"
        static let devoteePhone = "devoteePhone"
        static let devoteeRemarks = "devoteeRemarks"
        static let devoteeAccoId = "devoteeAccoID"
        static let devoteeAccoName = "devoteeAccoName"
        static let devoteePhoto = "devoteePhoto"
        static let devoteeIdImage = "devoteeIdImage"
        // static let rating = "rating"
    }
    
    //MARK: Initialization
    init?(firstName: String?, lastName: String?, devoteeKey: String, devoteeType: String?, devoteeIdType: String?, devoteeIdNumber: String?, devoteeStation: String?, devoteePhone: String?, devoteeRemarks: String?, devoteeAccoId: String?, devoteeAccoName: String?, devoteePhoto: UIImage?, devoteeIdImage: UIImage?){
        
        //Initilization should fail if there is no devotee Key or rating
       // guard !devoteeKey.isEmpty  else {
       //     return nil
       // }
        
        //Add other validations here (like raiting must be between 0 and 5 inclusively)
        //
        
        self.firstName = firstName
        self.lastName = lastName
        self.devoteeKey = devoteeKey
        self.devoteeType = devoteeType
        self.devoteeIdType = devoteeIdType
        self.devoteeIdNumber = devoteeIdNumber
        self.devoteeStation = devoteeStation
        self.devoteePhone = devoteePhone
        self.devoteeRemarks = devoteeRemarks
        self.devoteeAccoId = devoteeAccoId
        self.devoteeAccoName = devoteeAccoName
        self.devoteePhoto = devoteePhoto
        self.devoteeIdImage = devoteeIdImage
    }
}
