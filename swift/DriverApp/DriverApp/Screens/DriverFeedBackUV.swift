//
//  DriverFeedBackUV.swift
//  DriverApp
//
//  Created by NEW MAC on 24/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class DriverFeedBackUV: UIViewController, UITableViewDelegate, UITableViewDataSource {
    
    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!

    let generalFunc = GeneralFunctions()
    
    var loaderView:UIView!
    
    var dataArrList = [NSDictionary]()
    var textSizeArr = [CGFloat]()
    
    var nextPage_str = 1
    var isLoadingMore = false
    var isNextPageAvail = false
    
    var cntView:UIView!
    
    var isFirstLaunch = true

    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
//        self.contentView.addSubview(self.generalFunc.loadView(nibName: "DriverFeedBackScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        setData()
        
        
    }
    
    override func viewDidAppear(_ animated: Bool) {
        
        if(isFirstLaunch == true){
            
//            DispatchQueue.main.asyncAfter(deadline: DispatchTime.now() + Double(Int64(1 * Double(NSEC_PER_SEC))) / Double(NSEC_PER_SEC), execute: {
//                self.cntView.frame = self.contentView.frame
//                self.cntView.frame.size = CGSize(width: Application.screenSize.width, height: self.contentView.frame.height)
//                //            self.cntView.setNeedsLayout()
//            })
            
            cntView = self.generalFunc.loadView(nibName: "DriverFeedBackScreenDesign", uv: self, contentView: contentView)
            cntView.frame = CGRect(x:0, y:0, width: Application.screenSize.width, height: self.contentView.frame.height)
            self.contentView.addSubview(cntView)
            
            self.tableView.delegate = self
            
            self.tableView.dataSource = self
            self.tableView.tableFooterView = UIView()
            self.tableView.register(UINib(nibName: "DriverFeedBackTVCell", bundle: nil), forCellReuseIdentifier: "DriverFeedBackTVCell")
            
            self.dataArrList.removeAll()
            getDtata()
            
            isFirstLaunch = false
        }
        
    }

    func setData(){
        self.navigationItem.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RIDER_FEEDBACK")
        self.title = self.generalFunc.getLanguageLabel(origValue: "", key: "LBL_RIDER_FEEDBACK")
    }
    
    func getDtata(){
        if(loaderView == nil){
            loaderView =  self.generalFunc.addMDloader(contentView: self.contentView)
            loaderView.backgroundColor = Color.clear
        }else if(loaderView != nil && isLoadingMore == false){
            loaderView.isHidden = false
        }
        
        
        let parameters = ["type": "loadDriverFeedBack", "UserType": Utils.appUserType, "iDriverId": GeneralFunctions.getMemberd(), "page": self.nextPage_str.description]
        
        let exeWebServerUrl = ExeServerUrl(dict_data: parameters, currentView: self.view, isOpenLoader: false)
        exeWebServerUrl.executePostProcess(completionHandler: { (response) -> Void in
            
            if(response != ""){
                let dataDict = response.getJsonDataDict()
                
                if(dataDict.get("Action") == "1"){
                    
                    let dataArr = dataDict.getArrObj(Utils.message_str)
                    
                    for i in 0 ..< dataArr.count{
                        let dataTemp = dataArr[i] as! NSDictionary
                        
                        self.dataArrList += [dataTemp]
                        
                        var msgHeight = dataTemp.get("vMessage").height(withConstrainedWidth: Application.screenSize.width - 80, font: UIFont(name: "Roboto-Light", size: 18)!)
                        if(dataTemp.get("vMessage") == ""){
                            msgHeight = 0
                        }
                        
                        self.textSizeArr += [msgHeight]
                        
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
                    
                }else{
                    if(self.isLoadingMore == false){
                        _ = GeneralFunctions.addMsgLbl(contentView: self.cntView, msg: self.generalFunc.getLanguageLabel(origValue: "", key: dataDict.get("message")))
                    }else{
                        self.isNextPageAvail = false
                        self.nextPage_str = 0
                        
                        self.removeFooterView()
                    }
                    
                }
                
            }else{
                if(self.isLoadingMore == false){
                    self.generalFunc.setError(uv: self)
                }
            }
            self.isLoadingMore = false
            self.loaderView.isHidden = true
            
        })
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
        let cell = tableView.dequeueReusableCell(withIdentifier: "DriverFeedBackTVCell", for: indexPath) as! DriverFeedBackTVCell
        
        let item = self.dataArrList[indexPath.item]

        cell.dateLbl.text = Utils.convertDateFormateInAppLocal(date: Utils.convertDateGregorianToAppLocale(date: item.get("tDateOrig"), dateFormate: "yyyy-MM-dd HH:mm:ss"), toDateFormate: Utils.dateFormateInList)

        cell.containerView.layer.shadowOpacity = 0.5
        cell.containerView.layer.shadowOffset = CGSize(width: 0, height: 3)
        cell.containerView.layer.shadowColor = UIColor(hex: 0xe6e6e6).cgColor
        
        cell.nameLbl.text = item.get("vName")
        cell.userPicImgView.sd_setImage(with: URL(string: item.get("vImage")), placeholderImage:UIImage(named:"ic_no_pic_user"))
        
        cell.selectionStyle = .none
        cell.backgroundColor = UIColor.clear
        cell.ratingBar.rating = item.get("vRating1") == "" ? 0 : Float(item.get("vRating1"))!
        cell.commentLbl.text = item.get("vMessage") == "" ? "" : item.get("vMessage")
        cell.commentLbl.fitText()
        Utils.createRoundedView(view: cell.userPicImgView, borderColor: Color.clear, borderWidth: 0)
        
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat
    {
        return  155 + (self.textSizeArr[indexPath.item] == 0 ? -10 : self.textSizeArr[indexPath.item])
        
    }
    
    func scrollViewDidScroll(_ scrollView: UIScrollView) {
        let currentOffset = scrollView.contentOffset.y;
        let maximumOffset = scrollView.contentSize.height - scrollView.frame.size.height;
        
        
        if (maximumOffset - currentOffset <= 15) {
            
            if(isNextPageAvail==true && isLoadingMore==false){
                
                isLoadingMore=true
                
                getDtata()
            }
        }
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
