//
//  HelpCategoryListTVCell.swift
//  DriverApp
//
//  Created by NEW MAC on 15/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class HelpCategoryListTVCell: UITableViewCell {
    @IBOutlet weak var categoryNameLbl: MyLabel!
    @IBOutlet weak var rightImgView: UIImageView!

    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
