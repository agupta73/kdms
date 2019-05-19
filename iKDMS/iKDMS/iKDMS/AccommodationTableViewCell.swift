//
//  AccommodationTableViewCell.swift
//  iKDMS
//
//  Created by Gupta, Anil on 5/17/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import UIKit

class AccommodationTableViewCell: UITableViewCell {

    @IBOutlet weak var lblAccoName: UILabel!
    @IBOutlet weak var lblAvailableCount: UILabel!
    @IBOutlet weak var lblOccupiedCount: UILabel!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
