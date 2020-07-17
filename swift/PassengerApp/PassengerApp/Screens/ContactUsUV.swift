//
//  ContactUsUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 13/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class ContactUsUV: UIViewController, MyBtnClickDelegate, UITextViewDelegate {


    @IBOutlet weak var scrollView: UIScrollView!
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var reasonTxtField: MyTextField!
    @IBOutlet weak var queryLbl: MyLabel!
    @IBOutlet weak var queryTxtView: KMPlaceholderTextView!
    @IBOutlet weak var submitBtn: MyButton!
    
    let generalFunc = GeneralFunctions()
    
    var cntView:UIView!
    
    var isSafeAreaSet = false
    
    override func viewWillAppear(_ animated: Bool) {
        self.configureRTLView()
        Configurations.setAppThemeNavBar()
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()

//        cntView = self.generalFunc.loadView(nibName: "ContactUsScreenDesign", uv: self, contentView: contentView)
//        self.contentView.addSubview(cntView)
        
        self.scrollView.addSubview(self.generalFunc.loadView(nibName: "ContactUsScreenDesign", uv: self, contentView: scrollView))

        self.addBackBarBtn()
        setData()
        
        queryTxtView.delegate = self
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
//    override func viewDidLayoutSubviews() {
//        if(isSafeAreaSet == false){
//            if(cntView != nil){
//                self.cntView.frame = self.view.frame
//                cntView.frame.size.height = cntView.frame.size.height + GeneralFunctions.getSafeAreaInsets().bottom
//                print("ContentViewFrameSize----",cntView.frame.size.height)
//            }
//            isSafeAreaSet = true
//        }
//    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_HEADER_TXT")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_HEADER_TXT")
        
        self.reasonTxtField.setPlaceHolder(placeHolder: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RES_TO_CONTACT"))
        self.queryLbl.text = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_YOUR_QUERY")
 
        self.queryTxtView.placeholder = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_WRITE_EMAIL_TXT")
        self.submitBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_SEND_QUERY_BTN_TXT"))
        self.submitBtn.clickDelegate = self
        
        self.reasonTxtField.getTextField()!.isPlaceholderAnimated = true
    }

    func submitContactQuery(){
        
        let parameters = ["type":"sendContactQuery", "UserType": Utils.appUserType, "UserId": GeneralFunctions.getMemberd(),"message": queryTxtView.text!, "subject": Utils.getText(textField: reasonTxtField.getTextField()!)]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: true)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    self.reasonTxtField.setText(text: "")
                    self.queryTxtView.text = ""
                    
                }
                
                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))

                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
        })
    
    }
    func myBtnTapped(sender: MyButton) {
        if(sender == self.submitBtn){
            
            if(Utils.getText(textField: reasonTxtField.getTextField()!).characters.count == 0 || queryTxtView.text!.characters.count == 0){
                self.generalFunc.setError(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ENTER_DETAILS_TXT"))
            }else if(Utils.getText(textField: reasonTxtField.getTextField()!).characters.count < 2 || queryTxtView.text!.characters.count < 2){
                Utils.showSnakeBar(msg: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_FILL_PROPER_DETAILS"), uv: self)
            }else{
                submitContactQuery()
            }
        }
    }
}
