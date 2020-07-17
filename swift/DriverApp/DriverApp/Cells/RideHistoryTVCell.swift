//
//  RideHistoryTVCell.swift
//  DriverApp
//
//  Created by NEW MAC on 17/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class RideHistoryTVCell: UITableViewCell {
    @IBOutlet weak var bookingNoLbl: MyLabel!
    @IBOutlet weak var rideDateLbl: MyLabel!
    @IBOutlet weak var rideTypeLbl: MyLabel!
    @IBOutlet weak var pickUpLocHLbl: MyLabel!
    @IBOutlet weak var pickUpLocVLbl: MyLabel!
    @IBOutlet weak var destHLbl: MyLabel!
    @IBOutlet weak var destVLbl: MyLabel!
    @IBOutlet weak var statusHLbl: MyLabel!
    @IBOutlet weak var statusVLbl: MyLabel!
    @IBOutlet weak var cancelBtn: MyButton!
    @IBOutlet weak var statusView: UIView!
    @IBOutlet weak var dataView: UIView!
    @IBOutlet weak var locPinImgView: UIImageView!
    @IBOutlet weak var startTripBtn: MyButton!
    @IBOutlet weak var dashedView: UIView!

    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
