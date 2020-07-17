//
//  ListOfDocumentTVCell.swift
//  DriverApp
//
//  Created by NEW MAC on 05/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class ListOfDocumentTVCell: UITableViewCell {

    @IBOutlet weak var heightUploadOrManageDocView: NSLayoutConstraint!
    @IBOutlet weak var trailingDocNameLbl: NSLayoutConstraint!
    @IBOutlet weak var missingIconImgView: UIImageView!
    @IBOutlet weak var missingOrExpiredLbl: MyLabel!
    @IBOutlet weak var topDocNameLbl: NSLayoutConstraint!
    @IBOutlet weak var docNameLbl: MyLabel!
    @IBOutlet weak var rightArrowImgView: UIImageView!
    @IBOutlet weak var docImgView: UIImageView!
    @IBOutlet weak var manageDocBtn: MyButton!
    @IBOutlet weak var manageDocView: UIView!
    @IBOutlet weak var manageDocBtnWidth: NSLayoutConstraint!
    @IBOutlet weak var tempView: UIView!
    @IBOutlet weak var manageDocContainerView: UIView!
    @IBOutlet weak var manageStackView: UIStackView!
    
    override func awakeFromNib() {
        super.awakeFromNib()
        // Initialization code
    }

    override func setSelected(_ selected: Bool, animated: Bool) {
        super.setSelected(selected, animated: animated)

        // Configure the view for the selected state
    }

}
