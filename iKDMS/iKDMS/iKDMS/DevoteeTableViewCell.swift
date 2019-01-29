//
//  DevoteeTableViewCell.swift
//  iKDMS
//
//  Created by Gupta, Anil on 1/25/19.
//  Copyright © 2019 Gupta, Anil. All rights reserved.
//

import UIKit

class DevoteeTableViewCell: UITableViewCell {

    //MARK: Properties
    @IBOutlet weak var lblName: UILabel!
    @IBOutlet weak var imagePhoto: UIImageView!
    @IBOutlet weak var lblStation: UILabel!
    @IBOutlet weak var lblDevoteeKey: UILabel!
    @IBOutlet weak var lblAccommodation: UILabel!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
