//
//  MaintenancePageUV.swift
//  PassengerApp
//
//  Created by Tarwinder Singh on 08/01/18.
//  Copyright © 2018 V3Cube. All rights reserved.
//

import UIKit

class MaintenancePageUV: UIViewController, MyBtnClickDelegate {

    @IBOutlet weak var contactUsBtn: MyButton!
    @IBOutlet weak var maintennanceImgView: UIImageView!
    @IBOutlet weak var contentLbl: MyLabel!
    @IBOutlet weak var headerLbl: MyLabel!
    @IBOutlet weak var contentView: UIView!
    
    let generalFunc = GeneralFunctions()
    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
        Configurations.setDefaultStatusBar()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "MaintenancePageScreenDesign", uv: self, contentView: contentView))
        
        
        self.headerLbl.text = self.generalFunc.getLanguageLabel(origValue: "Under Maintenance", key: "LBL_MAINTENANCE_HEADER_MSG")
        self.contentLbl.text = self.generalFunc.getLanguageLabel(origValue: "Site is under maintenance. Please stay with us and check back later.", key: "LBL_MAINTENANCE_CONTENT_MSG")
        
        self.contactUsBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Contact Us", key: "LBL_CONTACT_US_TXT"))
        self.contactUsBtn.clickDelegate = self
        
        GeneralFunctions.setImgTintColor(imgView: maintennanceImgView, color: UIColor(hex: 0x333333))
    }

    func myBtnTapped(sender: MyButton) {
        if(sender == contactUsBtn){
            let contactUsUv = GeneralFunctions.instantiateViewController(pageName: "ContactUsUV") as! ContactUsUV
            self.pushToNavController(uv: contactUsUv)
        }
    }

}
