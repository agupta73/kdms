//
//  Accommodation.swift
//  iKDMS
//
//  Created by Gupta, Anil on 5/17/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import UIKit

class Accommodation {
    //MARK: Properties
    
    var accommodationName: String
    var availableCount: String
    var occupiedCount: String
    
    //MARK: Initialization
    init?(accommodationName: String, availableCount: String, occupiedCount: String) {
        if accommodationName.isEmpty {
            return nil
        }
        self.accommodationName = accommodationName
        self.availableCount = availableCount
        self.occupiedCount = occupiedCount
    }
}
