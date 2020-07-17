//
//  ManageServicesUV.swift
//  DriverApp
//
//  Created by NEW MAC on 03/10/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class ManageServicesUV: UIViewController, UITableViewDelegate, UITableViewDataSource {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!
    @IBOutlet weak var headerLbl: MyLabel!
    
    var cntView:UIView!
    
    var generalFunc = GeneralFunctions()
    
    var dataArrList = [NSDictionary]()
    
    var loaderView:UIView!
    
    var nextPage_str = 1
    var isLoadingMore = false
    var isNextPageAvail = false
    
    var iVehicleCategoryId = ""
    
    var isDataLoad = false
    var isSafeAreaSet = false
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        cntView = self.generalFunc.loadView(nibName: "ManageServicesScreenDesign", uv: self, contentView: contentView)
        
        self.contentView.addSubview(cntView)
        
        self.addBackBarBtn()
        self.setData()
        
        self.tableView.delegate = self
        self.tableView.dataSource = self
        self.tableView.bounces = false
        self.tableView.contentInset = UIEdgeInsets(top: 0, left: 0, bottom: 5 + GeneralFunctions.getSafeAreaInsets().bottom, right: 0)
        self.tableView.tableFooterView = UIView()
        self.tableView.register(UINib(nibName: "ManageServicesSubTVCell", bundle: nil), forCellReuseIdentifier: "ManageServicesSubTVCell")
        self.tableView.register(UINib(nibName: "ManageServicesHeaderTVCell", bundle: nil), forHeaderFooterViewReuseIdentifier: "ManageServicesHeaderTVCell")
        
        self.dataArrList.removeAll()
        
        
        self.headerLbl.isHidden = true
    }
    
    override func viewDidLayoutSubviews() {
        if(isSafeAreaSet == false){
            self.cntView.frame.size.height = cntView.frame.size.height + GeneralFunctions.getSafeAreaInsets().bottom
            isSafeAreaSet = true
        }
    }
    
    override func viewDidAppear(_ animated: Bool) {
        if(isDataLoad == false){
            
//            cntView.frame = self.contentView.frame
            
            self.dataArrList.removeAll()
            getDtata(isLoadingMore: self.isLoadingMore)
            
            isDataLoad = true
        }
    }
    
    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MANAGE_VEHICLES")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_MANAGE_VEHICLES")
        self.headerLbl.text = self.generalFunc.getLanguageLabel(origValue: "Select category below to add services you are going to provide", key: "LBL_MANAGE_SERVICE_INTRO_TXT")
        self.headerLbl.fitText()
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "ManageServicesSubTVCell", for: indexPath) as! ManageServicesSubTVCell
        
        var item:NSDictionary!
        
        if(self.iVehicleCategoryId != "" && self.iVehicleCategoryId != "0"){
            item = self.dataArrList[indexPath.row]
        }else{
            item = self.dataArrList[indexPath.section].getArrObj("SubCategory")[indexPath.row] as! NSDictionary
        }
        
        if(Configurations.isRTLMode()){
            cell.rightImgView.transform = CGAffineTransform(scaleX: -1, y: 1)
        }
        
        cell.subCategoryLbl.text = item.get("vTitle")
        GeneralFunctions.setImgTintColor(imgView: cell.rightImgView, color: UIColor(hex: 0x1c1c1c))
        return cell
    }
    
    
    func tableView(_ tableView: UITableView, heightForHeaderInSection section: Int) -> CGFloat {
        if(self.iVehicleCategoryId != "" && self.iVehicleCategoryId != "0"){
            return 0
        }
        return 50
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
        if(self.iVehicleCategoryId != "" && self.iVehicleCategoryId != "0"){
            return 60
        }
        return 40
    }
    
    
    func tableView(_ tableView: UITableView, viewForHeaderInSection section: Int) -> UIView? {
        // Here, we use NSFetchedResultsController
        // And we simply use the section name as title
        
        if(self.iVehicleCategoryId != "" && self.iVehicleCategoryId != "0"){
            return nil
        }
        
        // Dequeue with the reuse identifier
        let cell = self.tableView.dequeueReusableHeaderFooterView(withIdentifier: "ManageServicesHeaderTVCell") as! ManageServicesHeaderTVCell
        cell.headerLbl.text = self.dataArrList[section].get("vCategory")
        //        cell.backgroundColor = UIColor.UCAColor.AppThemeColor
        cell.headerLbl.backgroundColor = UIColor.UCAColor.AppThemeColor
        cell.headerLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
        
        return cell
    }
    
    //    func tableView(_ tableView: UITableView, willDisplayHeaderView view: UIView, forSection section: Int) {
    //        if(view .isKind(of: ManageServicesSubTVCell.self))
    //        {
    //            let cell = view as! ManageServicesSubTVCell
    //            cell.backgroundColor = UIColor.UCAColor.AppThemeColor
    ////            cell.headerLbl.textColor = UIColor.UCAColor.AppThemeTxtColor
    //        }
    //    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        //        UpdateServicesUV
        var item:NSDictionary!
        
        if(self.iVehicleCategoryId != "" && self.iVehicleCategoryId != "0"){
            item = self.dataArrList[indexPath.row]
        }else{
            item = self.dataArrList[indexPath.section].getArrObj("SubCategory")[indexPath.row] as! NSDictionary
        }
        
        let updateServiceUv = GeneralFunctions.instantiateViewController(pageName: "UpdateServicesUV") as! UpdateServicesUV
        updateServiceUv.vTitle = item.get("vTitle")
        updateServiceUv.iVehicleCategoryId = item.get("iVehicleCategoryId")
        
        self.pushToNavController(uv: updateServiceUv)
        
    }
    func numberOfSections(in tableView: UITableView) -> Int {
        // #warning Incomplete implementation, return the number of sections
        if(self.iVehicleCategoryId != "" && self.iVehicleCategoryId != "0"){
            return 1
        }
        return self.dataArrList.count
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete implementation, return the number of rows
        if(self.iVehicleCategoryId != "" && self.iVehicleCategoryId != "0"){
            return self.dataArrList.count
        }
        return self.dataArrList[section].getArrObj("SubCategory").count
    }
    
    func scrollViewDidScroll(_ scrollView: UIScrollView) {
        let currentOffset = scrollView.contentOffset.y;
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height;
        
        
        if (maximumOffset - currentOffset <= 15) {
            
            if(isNextPageAvail==true && isLoadingMore==false){
                
                isLoadingMore=true
                
                getDtata(isLoadingMore: isLoadingMore)
            }
        }
    }
    
    func getDtata(isLoadingMore:Bool){
        if(loaderView == nil){
            loaderView =  self.generalFunc.addMDloader(contentView: self.contentView)
            loaderView.backgroundColor = UIColor.clear
        }else if(loaderView != nil && isLoadingMore == false){
            loaderView.isHidden = false
        }
        
        
        let parameters = ["type": "getvehicleCategory", "UserType": Utils.appUserType, "iDriverId": GeneralFunctions.getMemberd(), "page": "\(self.nextPage_str)", "iVehicleCategoryId": iVehicleCategoryId]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let dataArr = dataDict.getArrObj(Utils.message_str)
                    
                    for i in 0 ..< dataArr.count{
                        let dataTemp = dataArr[i] as! NSDictionary
                        
                        self.dataArrList += [dataTemp]
                        
                    }
                    let NextPage = dataDict.get("NextPage")
                    
                    if(NextPage != "" && NextPage != "0"){
                        self.isNextPageAvail = true
                        self.nextPage_str = Int(NextPage)!
                        
                        self.addFooterView()
                    }else{
                        self.isNextPageAvail = false
                        self.nextPage_str = 0
                        
                        self.removeFooterView()
                    }
                    
                    self.tableView.reloadData()
                    
                    self.headerLbl.isHidden = false
                    
                }else{
                    if(isLoadingMore == false){
                        _ = GeneralFunctions.addMsgLbl(contentView: self.view, msg: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    }else{
                        self.isNextPageAvail = false
                        self.nextPage_str = 0
                        
                        self.removeFooterView()
                    }
                    
                }
                
            }else{
                if(isLoadingMore == false){
                    self.generalFunc.setError(uv: self)
                    
                }
            }
            
            self.isLoadingMore = false
            
            self.loaderView.isHidden = true
            
        })
        
    }
    
    func addFooterView(){
        let loaderView =  self.generalFunc.addMDloader(contentView: self.tableView, isAddToParent: false)
        loaderView.backgroundColor = UIColor.clear
        loaderView.frame = CGRect(x:0, y:0, width: Application.screenSize.width, height: 80)
        self.tableView.tableFooterView  = loaderView
        self.tableView.tableFooterView?.isHidden = false
    }
    
    func removeFooterView(){
        self.tableView.tableFooterView = UIView(frame: CGRect.zero)
        self.tableView.tableFooterView?.isHidden = true
    }
}
