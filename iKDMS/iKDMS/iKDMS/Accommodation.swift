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
    var availableCount: String?
    var occupiedCount: String?
    var accomodationKey: String?
    var reservedCount: String?
    var allocatedCount: String?
    var outOfAvailabilityCount: String?
    
    //MARK: Initialization
    init?(accommodationName: String, availableCount: String?, occupiedCount: String?, accomodationKey: String?, reservedCount: String?, allocatedCount: String?, outOfAvailabilityCount: String) {
        if accommodationName.isEmpty {
            return nil
        }
        self.accommodationName = accommodationName
        self.availableCount = availableCount
        self.occupiedCount = occupiedCount
        self.accomodationKey = accomodationKey
        self.reservedCount = reservedCount
        self.allocatedCount = allocatedCount
        self.outOfAvailabilityCount = outOfAvailabilityCount
    }
}
