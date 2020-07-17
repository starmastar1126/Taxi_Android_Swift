//
//  TransactionHistoryListTVCell.swift
//  DriverApp
//
//  Created by NEW MAC on 18/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class TransactionHistoryListTVCell: UITableViewCell {

    @IBOutlet weak var containerView: UIView!
    @IBOutlet weak var indicatorImgView: UIImageView!
    @IBOutlet weak var moneyLbl: MyLabel!
    @IBOutlet weak var descriptionLbl: MyLabel!
    @IBOutlet weak var dateLbl: MyLabel!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
