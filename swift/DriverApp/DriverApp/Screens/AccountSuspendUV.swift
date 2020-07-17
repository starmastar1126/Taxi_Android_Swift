//
//  AccountSuspendUV.swift
//  DriverApp
//
//  Created by NEW MAC on 20/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class AccountSuspendUV: UIViewController, MyBtnClickDelegate {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var contentLbl: MyLabel!
    @IBOutlet weak var contactUsBtn: MyButton!
    @IBOutlet weak var lockImgView: UIImageView!
    
    let generalFunc = GeneralFunctions()
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "AccountSuspendScreenDesign", uv: self, contentView: contentView))
        
        contentLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_STATUS_SUSPENDED_DRIVER")
        contentLbl.fitText()
        
        self.contactUsBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_TXT"))
        self.contactUsBtn.clickDelegate = self
        
    }
    
    func myBtnTapped(sender: MyButton) {
        if(sender == self.contactUsBtn){
            let contactUsUv = GeneralFunctions.instantiateViewController(pageName: "ContactUsUV") as! ContactUsUV
            self.pushToNavController(uv: contactUsUv)
        }
    }
}
