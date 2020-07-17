//
//  AboutUsUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 13/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class StaticPageUV: UIViewController {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var webView: UIWebView!
    
    let generalFunc = GeneralFunctions()
    
    var loaderView:UIView!
    
    var STATIC_PAGE_ID = "1"
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "StaticPageScreenDesign", uv: self, contentView: contentView))

        self.addBackBarBtn()
        
        self.webView.scrollView.bounces = false
        
        setData()
        
       loaderView =  self.generalFunc.addMDloader(contentView: self.view)
        loaderView.backgroundColor = UIColor.clear
        getAboutUsPageData()
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func setData(){
        if(STATIC_PAGE_ID == "1"){
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ABOUT_US_HEADER_TXT")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_ABOUT_US_HEADER_TXT")
        }else if(STATIC_PAGE_ID == "33"){
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PRIVACY_POLICY_TEXT")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_PRIVACY_POLICY_TEXT")
        }else if(STATIC_PAGE_ID == "4"){
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TERMS_AND_CONDITION")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_TERMS_AND_CONDITION")
        }else{
            self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DETAILS")
            self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_DETAILS")
        }
    }
    

    func getAboutUsPageData(){
        
        let parameters = ["type":"staticPage","iPageId": STATIC_PAGE_ID, "appType": Utils.appUserType, "iMemberId": GeneralFunctions.getMemberd(), "vLangCode": (GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) == nil ? "" : (GeneralFunctions.getValue(key: Utils.LANGUAGE_CODE_KEY) as! String))]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                let page_desc = dataDict.get("page_desc")
                let content = page_desc.replace("\n", withString: "<br>")
                
                if(Configurations.isRTLMode()){
                    self.webView.loadHTMLString("<html><body><p style=\"font-size:\(50/UIScreen.main.scale)px\" dir=\"rtl\">" + content + "</p></body></html>", baseURL: nil)
                }else{
                    self.webView.loadHTMLString("<html><body><p style=\"font-size:\(50/UIScreen.main.scale)px\">" + content + "</p></body></html>", baseURL: nil)
                }
                
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
            self.loaderView.isHidden = true
        })
    }
    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepare(for segue: UIStoryboardSegue, sender: Any?) {
        // Get the new view controller using segue.destinationViewController.
        // Pass the selected object to the new view controller.
    }
    */

}
