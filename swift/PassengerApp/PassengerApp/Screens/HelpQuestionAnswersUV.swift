//
//  HelpQuestionAnswersUV.swift
//  PassengerApp
//
//  Created by NEW MAC on 15/05/17.
//  Copyright © 2017 V3Cube. All rights reserved.
//

import UIKit

class HelpQuestionAnswersUV: UIViewController, UITableViewDelegate, UITableViewDataSource {

    @IBOutlet weak var contentView: UIView!
    @IBOutlet weak var tableView: UITableView!
    
    let generalFunc = GeneralFunctions()
    
    var selectedHelpCategoryItem:HelpCategoryItem!
    
    var questionList = [HelpQuestionAnswersItem]()
    
    var currentSelectedPosition = -1
    var currentHeight:CGFloat = 0
    
    var answerHeightContainer = [CGFloat]()
    var heightContainerDict = [String:CGFloat]()
    
    var isFirst = true
    
    
    override func viewWillAppear(_ animated: Bool) {
        
        self.configureRTLView()
        
    }
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.contentView.addSubview(self.generalFunc.loadView(nibName: "HelpQuestionAnswersScreenDesign", uv: self, contentView: contentView))
        
        self.addBackBarBtn()
        
        
        self.tableView.dataSource = self
        self.tableView.delegate = self
        
        
        self.tableView.register(CountryListTVCell.self, forCellReuseIdentifier: "HelpQuestionAnswersListTVCell")
        self.tableView.register(UINib(nibName: "HelpQuestionAnswersListTVCell", bundle: nil), forCellReuseIdentifier: "HelpQuestionAnswersListTVCell")
        self.tableView.tableFooterView = UIView()
        
        questionList.removeAll()
        
        for i in 0 ..< selectedHelpCategoryItem.questionList.count{
            let item = selectedHelpCategoryItem.questionList[i] as! NSDictionary
            
            let listItem = HelpQuestionAnswersItem(question: item.get("vTitle"), answer: item.get("tAnswer"))
            
            //            print(question_str)
            var answerHeight = item.get("tAnswer").getHTMLString(fontName: "Roboto-Light", fontSize: "17", textColor: "#676767", text: item.get("tAnswer")).height(withConstrainedWidth: Application.screenSize.width - 40)
            answerHeight = answerHeight + 10
//            print("answerHeight:\(i):\(answerHeight)")
            self.answerHeightContainer += [answerHeight]
            
            self.questionList += [listItem]
        }
        
        setData()
        
        self.tableView.reloadData()
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func setData(){
        self.navigationItem.title = selectedHelpCategoryItem.vTitle
        self.title = selectedHelpCategoryItem.vTitle
    }
    
    func numberOfSections(in tableView: UITableView) -> Int {
        
        return 1
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete implementation, return the number of rows
        return questionList.count
    }

    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "HelpQuestionAnswersListTVCell", for: indexPath) as! HelpQuestionAnswersListTVCell
        
        let item = questionList[indexPath.row]
        
        //        let historyJson = item.questionsJson
        let title_category = item.question
        cell.questionLbl.text = title_category
        
//        if(isFirst){
//            cell.questionLbl.removeGestureRecognizer(cell.questionLbl.tapGue)
//            cell.answerLbl.removeGestureRecognizer(cell.answerLbl.tapGue)
//            isFirst = false
//        }
        GeneralFunctions.setImgTintColor(imgView: cell.arrowImgView, color: UIColor(hex: 0x9f9f9f))

        cell.questionLbl.removeGestureRecognizer(cell.questionLbl.tapGue)
        cell.answerLbl.removeGestureRecognizer(cell.answerLbl.tapGue)

        let content = item.answer.replace("\n", withString: "<br>")
        

        if(currentSelectedPosition == -1 || currentSelectedPosition != indexPath.item){
            cell.arrowImgView.transform = CGAffineTransform(rotationAngle: 90 * CGFloat(CGFloat.pi/180) )
        }else{
            cell.arrowImgView.transform = CGAffineTransform(rotationAngle: -90 * CGFloat(CGFloat.pi/180) )
        }
        self.currentHeight = 72
        
        if(currentSelectedPosition == indexPath.item){
           
            cell.answerLbl.numberOfLines = 0
            cell.answerLbl.textAlignment = .right
            cell.answerLbl.setHTMLFromString(text: content)
            cell.answerLbl.fitText()
            cell.answerLbl.isHidden = false
            
           
        }else{
            cell.answerLbl.isHidden = true
        }
        
        
        
        cell.selectionStyle = .none;
        cell.backgroundColor = UIColor.clear
        return cell
    }
    
    func tableView(_ tableView: UITableView, didSelectRowAt indexPath: IndexPath) {
        
        if(currentSelectedPosition != indexPath.row){
            currentSelectedPosition = indexPath.item
        }else{
            currentSelectedPosition = -1
        }
        
        self.tableView.reloadData()
        
    }
    
    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat
    {
        if(currentSelectedPosition == -1 || currentSelectedPosition != indexPath.item){
            return 72;
        }else{
//            if(heightContainerDict["KEY_\(indexPath.item)"] == nil){
//                return 72
//            }
//            return heightContainerDict["KEY_\(indexPath.item)"]! + 72;
            return self.answerHeightContainer[indexPath.item] + 72
//            return currentHeight
        }

        
    }
    
//    func tableView(_ tableView: UITableView, heightForRowAt indexPath: IndexPath) -> CGFloat {
//        return UITableViewAutomaticDimension
//    }
    
//    func tableView(_ tableView: UITableView, estimatedHeightForRowAt indexPath: IndexPath) -> CGFloat {
//        if(currentSelectedPosition == -1 || currentSelectedPosition != indexPath.item){
//            return 72;
//        }else{
//            if(heightContainerDict["KEY_\(indexPath.item)"] == nil){
//                return 72
//            }
//            return heightContainerDict["KEY_\(indexPath.item)"]! + 72;
//        }
//
//    }

}

class HelpQuestionAnswersItem {
    
    var question:String!
    var answer:String!
    
    // MARK: Initialization
    
    init(question: String, answer:String) {
        // Initialize stored properties.
        self.question = question
        self.answer = answer
        
    }
}
