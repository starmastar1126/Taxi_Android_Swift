//
//  ListOfDocumentUV.swift
//  DriverApp
//
//  Created by NEW MAC on 05/06/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit
import SafariServices

class ListOfDocumentUV: UIViewController, UITableViewDelegate, UITableViewDataSource, MyBtnClickDelegate {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!
    
    let generalFunc = GeneralFunctions()
    
    var dataArrList = [NSDictionary]()
   
    var manageVehiUV:ManageVehiclesUV!
    
    var fromAddVehicle = false

    var iDriverVehicleId = ""
    
    var LIST_TYPE = "driver"
    
    var currentSelectedPosition = -1
    
    var cntView:UIView!
    
    var isSafeAreaSet = false
    
    var isPageLoad = false
    
    var loaderView:UIView!
    var contactUsPress = false
    var docNameHeightContainer = [CGFloat]()
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        if(self.contactUsPress == true){
            self.navigationController?.popViewController(animated: false)
        }
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        cntView = self.generalFunc.loadView(nibName: "ListOfDocumentScreenDesign", uv: self, contentView: contentView)
        
        self.contentView.addSubview(cntView)
        
//        self.contentView.addSubview(self.generalFunc.loadView(nibName: "ListOfDocumentScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        
        self.tableView.dataSource = self
        self.tableView.delegate = self
        self.tableView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: GeneralFunctions.getSafeAreaInsets().bottom + 9, right: 0)
        
        
        self.tableView.register(UINib(nibName: "ListOfDocumentTVCell", bundle: nil), forCellReuseIdentifier: "ListOfDocumentTVCell")
        self.tableView.tableFooterView = UIView()
        
        setData()
        
    }
    
    override func closeCurrentScreen() {
        var isVehicleListingAvail = false
        for i in 0..<self.navigationController!.viewControllers.count{
            let viewController = self.navigationController!.viewControllers[i]
            
            if(viewController.isKind(of: ManageVehiclesUV.self)){
                isVehicleListingAvail = true
                break
            }
        }
        
        if(isVehicleListingAvail == false){
            self.navigationController?.popToRootViewController(animated: true)
        }else{
//            super.closeCurrentScreen()
            if(fromAddVehicle == true){
                self.performSegue(withIdentifier: "unwindToManageVehicles", sender: self)
            }else{
                super.closeCurrentScreen()
            }
        }
    }
    
    override func viewDidLayoutSubviews() {
        
        if(isSafeAreaSet == false){
            
            if(cntView != nil){
                cntView.frame.size.height = cntView.frame.size.height + GeneralFunctions.getSafeAreaInsets().bottom
            }
            
            isSafeAreaSet = true
        }
    }
    
    override func viewDidAppear(_ animated: Bool) {
        
//        DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(1 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
//            //            self.cntView.frame = self.view.frame
//            self.cntView.frame.size = CGSize(width: Application.screenSize.width, height: self.view.frame.height)
//            //            self.cntView.setNeedsLayout()
//        })
        
        if(isPageLoad == false){
            getListData()
            isPageLoad = true
        }
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "Select Doument", key: "LBL_SELECT_DOC")
        self.title = self.generalFunc.getLanguageLabel(origValue: "Select Doument", key: "LBL_SELECT_DOC")
    }
    
    func getListData(){
        self.docNameHeightContainer = []
        self.currentSelectedPosition = -1
        self.dataArrList.removeAll()
        self.tableView.reloadData()
        
        if(loaderView == nil){
            loaderView =  self.generalFunc.addMDloader(contentView: self.view)
            loaderView.backgroundColor = UIColor.clear
        }else if(loaderView != nil){
            loaderView.isHidden = false
        }
        
        let parameters = ["type":"displayDocList","iMemberId": GeneralFunctions.getMemberd(), "doc_usertype": self.LIST_TYPE, "iDriverVehicleId": iDriverVehicleId]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.setDeviceTokenGenerate(isDeviceTokenGenerate: false)
        exeWebServerUrl.currInstance = exeWebServerUrl
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let msgArr = dataDict.getArrObj(Utils.message_str)
                    
                    for i in 0..<msgArr.count{
                        
                        self.dataArrList += [msgArr[i] as! NSDictionary]
                        
                    }
                    
                    if(self.dataArrList.count < 1){
                        self.noDataCall(dataDict: dataDict)
                    }
                    
                    for i in 0 ..< self.dataArrList.count{
                        let docName = self.dataArrList[i].get("doc_name")
                        
                        let docNameHeight = docName.height(withConstrainedWidth: Application.screenSize.width - 103, font: UIFont(name: "Roboto-Medium", size: 20)!) - 24
                        
                        self.docNameHeightContainer += [docNameHeight]

                    }
                    self.tableView.reloadData()
                    
                }else{
                    self.noDataCall(dataDict: dataDict)
                }
                
            }else{
                self.generalFunc.setError(uv: self)
            }
            
            self.loaderView.isHidden = true
        })
        
    }

    func noDataCall(dataDict:NSDictionary){
        self.generalFunc.setAlertMessage(uv: self, title: "", content: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get(Utils.message_str)), positiveBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_CONTACT_US_TXT").uppercased(), nagativeBtn: self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_BTN_OK_TXT").uppercased(), completionHandler: { (btnClickedId) in
            
            if(btnClickedId == 1){
                if(self.manageVehiUV != nil){
                    self.navigationController?.popToViewController(self.manageVehiUV, animated: true)
                }else{
                    self.navigationController?.popViewController(animated: true)
                }
            }else{
                let contactUsUv = GeneralFunctions.instantiateViewController(pageName: "ContactUsUV") as! ContactUsUV
                self.contactUsPress = true
                (self.navigationDrawerController?.rootViewController as! UINavigationController).pushViewController(contactUsUv, animated: true)
            }
            
        })
        return
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        // #warning Incomplete implementation, return the number of sections
        return 1
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete implementation, return the number of rows
        
        return self.dataArrList.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ListOfDocumentTVCell", for: indexPath) as! ListOfDocumentTVCell
        
        let item = self.dataArrList[indexPath.item]
        
        cell.docNameLbl.text = item.get("doc_name")
        
        cell.docNameLbl.fitText()
        
        if(currentSelectedPosition == indexPath.item){
            cell.rightArrowImgView.transform = CGAffineTransform(rotationAngle: -90 * CGFloat(CGFloat.pi/180) )
            cell.manageDocView.isHidden = false
        }else{
            cell.rightArrowImgView.transform = CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180) )
            cell.manageDocView.isHidden = true
        }
        
        GeneralFunctions.setImgTintColor(imgView: cell.rightArrowImgView, color: UIColor(hex: 0x9f9f9f))
        cell.manageDocBtn.tag = indexPath.item
        cell.manageDocBtn.clickDelegate = self
        
        cell.docImgView.tag = indexPath.item
    
        cell.heightUploadOrManageDocView.constant = 93
        
        if(item.get("doc_file") != ""){
            cell.docImgView.image = UIImage(named: "ic_doc_on")
            cell.docImgView.isHidden = false
            cell.tempView.isHidden = false
            cell.manageDocBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Manage", key: "LBL_MANAGE"))
            cell.manageDocBtnWidth.constant = 125
            
            if item.get("EXPIRE_DOCUMENT").uppercased() == "NO"{
                cell.missingOrExpiredLbl.isHidden = true
                cell.missingIconImgView.isHidden = true
                cell.trailingDocNameLbl.constant = 10
                cell.topDocNameLbl.constant = 15
            }else{
                cell.missingOrExpiredLbl.isHidden = false
                cell.missingIconImgView.isHidden = false
                cell.trailingDocNameLbl.constant = 23
                cell.topDocNameLbl.constant = 9
                cell.missingOrExpiredLbl.text = self.generalFunc.getLanguageLabel(origValue: "Missing", key: "LBL_EXPIRED_TXT")
            }
        }else{
            cell.docImgView.image = UIImage(named: "ic_doc_off")
            cell.docImgView.isHidden = true
            cell.tempView.isHidden = true
            cell.manageDocBtn.setButtonTitle(buttonTitle: self.generalFunc.getLanguageLabel(origValue: "Upload Document", key: "LBL_UPLOAD_DOC"))
            cell.missingOrExpiredLbl.isHidden = false
            cell.missingIconImgView.isHidden = false
            cell.trailingDocNameLbl.constant = 23
            cell.topDocNameLbl.constant = 9
            cell.missingOrExpiredLbl.text = self.generalFunc.getLanguageLabel(origValue: "Missing", key: "LBL_MISSING_TXT")
        }
        
        cell.manageStackView.setNeedsDisplay()
        cell.manageStackView.setNeedsLayout()
        cell.manageStackView.layoutSubviews()
        
        let docImgTapGue = UITapGestureRecognizer()
        docImgTapGue.addTarget(self, action: #selector(self.docImgViewTapped(sender:)))
        cell.docImgView.isUserInteractionEnabled = true
        cell.docImgView.addGestureRecognizer(docImgTapGue)
        
        cell.selectionStyle = .none
        cell.backgroundColor = UIColor.clear
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        if(self.currentSelectedPosition == indexPath.item){
            self.currentSelectedPosition = -1
        }else{
            self.currentSelectedPosition = indexPath.item
        }
        self.tableView.reloadData()
    }

    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat{
        if(currentSelectedPosition == -1 || currentSelectedPosition != indexPath.item){
            return self.docNameHeightContainer[indexPath.item] + 72
        }else{
            return self.docNameHeightContainer[indexPath.item] + 165
        }
    }
    
    func docImgViewTapped(sender:UITapGestureRecognizer){
        let item = self.dataArrList[sender.view!.tag]
        
        if(item.get("doc_file") != ""){
            self.present(SFSafariViewController(url: URL(string: item.get("vimage"))!), animated: true, completion: nil)
        }
    }
    
    func myBtnTapped(sender: MyButton) {
        let uploadDocUv = GeneralFunctions.instantiateViewController(pageName: "UploadDocUV") as! UploadDocUV
        uploadDocUv.dataDict = self.dataArrList[sender.tag]
        uploadDocUv.DOC_TYPE = self.LIST_TYPE
        uploadDocUv.iDriverVehicleId = self.iDriverVehicleId
        self.pushToNavController(uv: uploadDocUv)
    }
    
    @IBAction func unwindToDocumentList(_ segue:UIStoryboardSegue) {
        if(segue.source.isKind(of: UploadDocUV.self)){
            self.dataArrList.removeAll()
            self.tableView.reloadData()
            getListData()
        }
    }
}
