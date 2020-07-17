//
//  GPAutoCompleteListTVCell.swift
//  PassengerApp
//
//  Created by NEW MAC on 25/09/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class GPAutoCompleteListTVCell: UITableViewCell {

    @IBOutlet weak var mainTxtLbl: MyLabel!
    @IBOutlet weak var secondaryTxtLbl: MyLabel!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
