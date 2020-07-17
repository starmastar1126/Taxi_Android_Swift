//
//  ManageVehiclesTVCell.swift
//  DriverApp
//
//  Created by NEW MAC on 02/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class ManageVehiclesTVCell: UITableViewCell {

    @IBOutlet weak var containerView: UIView!
    @IBOutlet weak var vehicleNameLbl: MyLabel!
    @IBOutlet weak var vOtherInfoLbl: MyLabel!
    @IBOutlet weak var statusLbl: MyLabel!
    @IBOutlet weak var deleteVehicleImgView: UIImageView!
    @IBOutlet weak var editVehicleImgView: UIImageView!
    @IBOutlet weak var uploadVehicleDocumentsImgView: UIImageView!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
