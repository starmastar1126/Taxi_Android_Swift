//
//  UpdateServicesTVCell.swift
//  DriverApp
//
//  Created by NEW MAC on 03/10/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class UpdateServicesTVCell: UITableViewCell {

    @IBOutlet weak var serviceCHKContainerView: UIView!
    @IBOutlet weak var serviceLbl: MyLabel!
    @IBOutlet weak var subTitleLbl: MyLabel!
    @IBOutlet weak var serviceChkBox: BEMCheckBox!
    @IBOutlet weak var serviceAmountAreaView: UIView!
    @IBOutlet weak var serviceAmountLbl: MyLabel!
    @IBOutlet weak var priceLbl: MyLabel!
    @IBOutlet weak var editBtn: MyButton!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
