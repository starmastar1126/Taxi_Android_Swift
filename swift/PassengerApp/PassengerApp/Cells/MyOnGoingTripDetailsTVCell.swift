//
//  MyOnGoingTripDetailsTVCell.swift
//  PassengerApp
//
//  Created by NEW MAC on 18/07/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class MyOnGoingTripDetailsTVCell: UITableViewCell {

    @IBOutlet weak var noLbl: MyLabel!
    @IBOutlet weak var progressMsgLbl: MyLabel!
    @IBOutlet weak var progressTimeLbl: MyLabel!
    @IBOutlet weak var progressPastTimeLbl: MyLabel!
    @IBOutlet weak var noView: UIView!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
